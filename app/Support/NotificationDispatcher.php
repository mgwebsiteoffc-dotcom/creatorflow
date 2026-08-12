<?php

namespace App\Support;

use App\Models\AppNotification;
use App\Models\NotificationTemplate;
use App\Models\PlatformSetting;
use Illuminate\Support\Str;

/**
 * Fan-out for every domain event: sends email + WhatsApp + in-app based on
 * per-event admin toggles. Recipient is inferred from the audience of the
 * template. All variables in {{like_this}} are interpolated from $vars.
 */
class NotificationDispatcher
{
    public function __construct(
        protected MailService $mail,
        protected WhatifyService $whatify,
    ) {}

    /**
     * @param string $eventKey    e.g. 'creator.invited'
     * @param array  $recipient   ['email' => ..., 'phone' => ..., 'name' => ..., 'type' => 'creator'|'user', 'id' => 123]
     * @param array  $vars        merged into the template with str_replace
     * @param array  $inappMeta   overrides for the in-app notification row (title/body/link)
     */
    public function fire(string $eventKey, array $recipient, array $vars = [], array $inappMeta = []): array
    {
        $template = $this->findTemplate($eventKey);
        if (! $template) return ['ok' => false, 'error' => "No template for {$eventKey}"];

        $vars = array_merge($this->defaultVars(), $vars);
        $result = ['email' => null, 'whatsapp' => null, 'inapp' => null];

        // ─── In-app ───
        if ($template->inapp_enabled && ! empty($recipient['type']) && ! empty($recipient['id'])) {
            try {
                if (SchemaCheck::has('notifications')) {
                    AppNotification::safeCreate([
                        'id'             => (string) Str::uuid(),
                        'recipient_type' => $recipient['type'],
                        'recipient_id'   => (int) $recipient['id'],
                        'type'           => $eventKey,
                        'data'           => [
                            'title' => $inappMeta['title'] ?? $this->interpolate($template->email_subject ?: $template->label, $vars),
                            'body'  => $inappMeta['body']  ?? $this->interpolate(Str::limit($template->email_body ?? '', 200), $vars),
                            'link'  => $inappMeta['link']  ?? ($vars['link'] ?? null),
                            'vars'  => $vars,
                        ],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                    $result['inapp'] = 'ok';
                }
            } catch (\Throwable $e) { $result['inapp'] = 'err:'.$e->getMessage(); }
        }

        // ─── Email ───
        if ($template->email_enabled && ! empty($recipient['email'])) {
            $subject = $this->interpolate($template->email_subject ?: $template->label, $vars);
            $body    = $this->interpolate($template->email_body ?: '', $vars);
            $html    = $this->wrapHtml($subject, $body, $vars['link'] ?? null);
            $sent    = $this->mail->send($recipient['email'], $subject, $html, strip_tags($body));
            $result['email'] = $sent['ok'] ? ($sent['driver'] ?? 'ok') : 'err:'.($sent['error'] ?? '');
        }

        // ─── WhatsApp (Whatify) ───
        if ($template->whatsapp_enabled && ! empty($recipient['phone']) && $this->whatify->isEnabled()) {
            $tpl = $template->whatsapp_template_name;
            if ($tpl) {
                // Template mode — interpolate each placeholder as a body param.
                $params = array_map(
                    fn ($p) => $this->interpolate((string) $p, $vars),
                    (array) $template->whatsapp_body_params
                );
                $sent = $this->whatify->sendTemplate($recipient['phone'], $tpl, $params);
            } else {
                // Free-text mode — only works inside 24h window.
                $text = $this->interpolate($template->email_body ?: '', $vars);
                $sent = $this->whatify->sendMessage($recipient['phone'], $text);
            }
            $result['whatsapp'] = $sent['ok'] ? 'ok' : 'err:'.($sent['error'] ?? '');
        }

        return $result;
    }

    protected function findTemplate(string $key): ?NotificationTemplate
    {
        if (! SchemaCheck::has('notification_templates')) return null;
        return NotificationTemplate::where('event_key', $key)->first();
    }

    protected function defaultVars(): array
    {
        return [
            'platform'      => 'CreatorPlex',
            'support_email' => PlatformSetting::current()->mail_reply_to ?: 'hello@creatorplex.in',
            'app_url'       => rtrim(config('app.url') ?: url('/'), '/'),
        ];
    }

    /**
     * Replace every {{key}} in $text with $vars[key].
     */
    protected function interpolate(string $text, array $vars): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($vars) {
            return (string) ($vars[$m[1]] ?? $m[0]);
        }, $text);
    }

    /**
     * Wrap a plain-text body in a branded HTML template.
     */
    protected function wrapHtml(string $subject, string $body, ?string $link = null): string
    {
        $body = nl2br(e($body));
        $btn  = $link
            ? '<p style="margin:28px 0"><a href="'.e($link).'" style="display:inline-block;background:linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);color:#fff;font-weight:700;text-decoration:none;padding:12px 22px;border-radius:12px;">Open in CreatorPlex →</a></p>'
            : '';
        $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
        return <<<HTML
<!doctype html>
<html><head><meta charset="utf-8"><title>{$subject}</title></head>
<body style="margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;background:#f8fafc;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 12px 32px -12px rgba(15,23,42,.08);">
                <tr><td style="padding:20px 28px 0;">
                    <a href="{$siteUrl}" style="text-decoration:none;color:#0f172a;font-weight:900;letter-spacing:-.5px;font-size:20px;">
                        <span style="display:inline-block;width:28px;height:28px;line-height:28px;text-align:center;border-radius:8px;background:linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);color:#fff;font-size:11px;font-weight:900;margin-right:8px;vertical-align:middle;">CP</span>CreatorPlex
                    </a>
                </td></tr>
                <tr><td style="padding:24px 28px 8px;font-size:15px;line-height:1.6;color:#334155;">
                    <p style="margin:0 0 16px;font-size:20px;font-weight:800;color:#0f172a;">{$subject}</p>
                    <div>{$body}</div>
                    {$btn}
                </td></tr>
                <tr><td style="padding:20px 28px 28px;border-top:1px solid #f1f5f9;font-size:12px;color:#94a3b8;">
                    You're getting this because you're active on CreatorPlex.<br>
                    <a href="{$siteUrl}/notifications" style="color:#7c3aed;">Manage your notifications</a>.
                </td></tr>
            </table>
        </td></tr>
    </table>
</body></html>
HTML;
    }
}

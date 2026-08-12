<?php

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Thin transactional email helper.
 *
 * Resolves the mail provider at call-time from platform_settings (admin
 * managed) so a support agent can rotate the key without a deploy.
 *
 * Supported drivers:
 *   • log         — write to storage/logs/laravel.log (default; safe for demos)
 *   • resend      — https://resend.com (Bearer API key)
 *   • mailersend  — https://mailersend.com (Bearer API key)
 *   • smtp        — configure via mail.php / .env (mail_api_key ignored)
 */
class MailService
{
    public function __construct() {}

    protected function settings(): PlatformSetting
    {
        return PlatformSetting::current();
    }

    /**
     * Send a plain HTML/text email.
     */
    public function send(string $to, string $subject, string $html, ?string $text = null): array
    {
        $s = $this->settings();
        $from = $s->mail_from_address ?: 'hello@creatorplex.in';
        $fromName = $s->mail_from_name ?: 'CreatorPlex';
        $replyTo = $s->mail_reply_to ?: $from;

        try {
            return match ($s->mail_driver ?: 'log') {
                'resend'     => $this->sendResend($s->mail_api_key, $from, $fromName, $replyTo, $to, $subject, $html, $text),
                'mailersend' => $this->sendMailerSend($s->mail_api_key, $from, $fromName, $replyTo, $to, $subject, $html, $text),
                'smtp'       => $this->sendSmtp($from, $fromName, $replyTo, $to, $subject, $html, $text),
                default      => $this->sendLog($from, $to, $subject, $html),
            };
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendLog(string $from, string $to, string $subject, string $html): array
    {
        Log::info("[MAIL/log] from={$from} to={$to} subject=\"{$subject}\"");
        Log::debug($html);
        return ['ok' => true, 'driver' => 'log'];
    }

    protected function sendResend(?string $key, string $from, string $fromName, string $replyTo, string $to, string $subject, string $html, ?string $text): array
    {
        if (empty($key)) throw new \RuntimeException('Resend API key not set. Save one in Admin → Mail settings.');
        $r = Http::withToken($key)->asJson()->post('https://api.resend.com/emails', [
            'from'     => "{$fromName} <{$from}>",
            'to'       => [$to],
            'subject'  => $subject,
            'html'     => $html,
            'text'     => $text,
            'reply_to' => $replyTo,
        ])->throw()->json();
        return ['ok' => true, 'driver' => 'resend', 'id' => $r['id'] ?? null];
    }

    protected function sendMailerSend(?string $key, string $from, string $fromName, string $replyTo, string $to, string $subject, string $html, ?string $text): array
    {
        if (empty($key)) throw new \RuntimeException('MailerSend API key not set. Save one in Admin → Mail settings.');
        $r = Http::withToken($key)->asJson()->post('https://api.mailersend.com/v1/email', [
            'from'     => ['email' => $from, 'name' => $fromName],
            'to'       => [['email' => $to]],
            'reply_to' => ['email' => $replyTo],
            'subject'  => $subject,
            'html'     => $html,
            'text'     => $text ?? strip_tags($html),
        ])->throw();
        return ['ok' => true, 'driver' => 'mailersend', 'status' => $r->status()];
    }

    protected function sendSmtp(string $from, string $fromName, string $replyTo, string $to, string $subject, string $html, ?string $text): array
    {
        Config::set('mail.from.address', $from);
        Config::set('mail.from.name', $fromName);

        Mail::html($html, function ($m) use ($to, $subject, $replyTo) {
            $m->to($to)->subject($subject)->replyTo($replyTo);
        });
        return ['ok' => true, 'driver' => 'smtp'];
    }
}

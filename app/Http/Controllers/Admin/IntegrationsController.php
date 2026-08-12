<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\MailService;
use App\Support\RazorpayService;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

/**
 * Superadmin integrations: mail provider, Razorpay, analytics/verification
 * scripts, and roadmap feature flags. Everything is DB-driven so keys can
 * rotate without a deploy.
 */
class IntegrationsController extends Controller
{
    public function edit()
    {
        $schemaMissing  = ! SchemaCheck::has('platform_settings');
        $columnsMissing = $schemaMissing || ! \Illuminate\Support\Facades\Schema::hasColumn('platform_settings', 'mail_driver');

        $settings = $schemaMissing ? new PlatformSetting() : PlatformSetting::current();
        $features = PlatformSetting::features();

        return view('admin.integrations.edit', compact('settings', 'schemaMissing', 'columnsMissing', 'features'));
    }

    /* ─────────────────────────── MAIL ─────────────────────────── */

    public function updateMail(Request $request)
    {
        $this->ensureMigrated();

        $data = $request->validate([
            'mail_driver'       => ['required', 'in:log,resend,mailersend,smtp'],
            'mail_api_key'      => ['nullable', 'string', 'max:300'],
            'mail_from_address' => ['nullable', 'email'],
            'mail_from_name'    => ['nullable', 'string', 'max:120'],
            'mail_reply_to'     => ['nullable', 'email'],
        ]);

        // Preserve existing key if the input still shows the mask or is empty.
        if (empty($data['mail_api_key']) || str_contains((string) $data['mail_api_key'], '•')) {
            unset($data['mail_api_key']);
        }

        PlatformSetting::current()->update(array_filter($data, fn ($v) => $v !== null) + [
            'mail_last_test_status' => null,
        ]);

        return back()->with('status', 'Mail settings saved.');
    }

    public function testMail(Request $request, MailService $mail)
    {
        $this->ensureMigrated();

        $to = $request->validate(['to' => ['required', 'email']])['to'];
        $result = $mail->send(
            $to,
            'CreatorPlex test email ✅',
            '<h2>You\'re connected!</h2><p>This is a test email from CreatorPlex. If you see this, your mail provider is set up correctly.</p>',
            "You're connected! This is a test email from CreatorPlex."
        );

        PlatformSetting::current()->update([
            'mail_last_tested_at'   => now(),
            'mail_last_test_status' => $result['ok'] ? 'ok:'.($result['driver'] ?? '') : 'error',
        ]);

        return back()->with(
            $result['ok'] ? 'status' : 'error',
            $result['ok'] ? "✓ Test email sent to {$to} via ".($result['driver'] ?? '').'.' : '❌ Failed: '.($result['error'] ?? 'unknown')
        );
    }

    /* ─────────────────────────── ANALYTICS ─────────────────────────── */

    public function updateAnalytics(Request $request)
    {
        $this->ensureMigrated();

        $data = $request->validate([
            'ga4_measurement_id'       => ['nullable', 'string', 'max:40'],
            'gtm_container_id'         => ['nullable', 'string', 'max:40'],
            'meta_pixel_id'            => ['nullable', 'string', 'max:40'],
            'linkedin_partner_id'      => ['nullable', 'string', 'max:40'],
            'hotjar_id'                => ['nullable', 'string', 'max:40'],
            'google_site_verification' => ['nullable', 'string', 'max:100'],
            'bing_site_verification'   => ['nullable', 'string', 'max:100'],
            'custom_head_html'         => ['nullable', 'string', 'max:20000'],
            'custom_body_html'         => ['nullable', 'string', 'max:20000'],
        ]);

        PlatformSetting::current()->update($data);

        return back()->with('status', 'Analytics + verification scripts saved. Reload the site to see them.');
    }

    /* ─────────────────────────── RAZORPAY ─────────────────────────── */

    public function updateRazorpay(Request $request)
    {
        $this->ensureMigrated();

        $data = $request->validate([
            'razorpay_mode'           => ['required', 'in:test,live'],
            'razorpay_key_id'         => ['nullable', 'string', 'max:80'],
            'razorpay_key_secret'     => ['nullable', 'string', 'max:200'],
            'razorpay_webhook_secret' => ['nullable', 'string', 'max:200'],
        ]);

        foreach (['razorpay_key_secret', 'razorpay_webhook_secret'] as $secret) {
            if (empty($data[$secret]) || str_contains((string) $data[$secret], '•')) {
                unset($data[$secret]);
            }
        }

        PlatformSetting::current()->update(array_filter($data, fn ($v) => $v !== null) + [
            'razorpay_last_test_status' => null,
        ]);

        return back()->with('status', 'Razorpay settings saved.');
    }

    public function testRazorpay(RazorpayService $rz)
    {
        $this->ensureMigrated();

        try {
            $ping = $rz->ping();
            PlatformSetting::current()->update([
                'razorpay_last_tested_at'   => now(),
                'razorpay_last_test_status' => $ping['ok'] ? 'ok:'.$ping['mode'] : 'error',
            ]);
            return back()->with(
                $ping['ok'] ? 'status' : 'error',
                $ping['ok'] ? "✓ Connected to Razorpay ({$ping['mode']} mode)." : "❌ HTTP {$ping['status']}"
            );
        } catch (\Throwable $e) {
            PlatformSetting::current()->update(['razorpay_last_tested_at' => now(), 'razorpay_last_test_status' => 'error']);
            return back()->with('error', '❌ '.$e->getMessage());
        }
    }

    /* ─────────────────────────── WHATIFY (WhatsApp) ─────────────────────────── */

    public function updateWhatify(Request $request)
    {
        $this->ensureMigrated();

        $data = $request->validate([
            'whatify_enabled'    => ['nullable', 'boolean'],
            'whatify_api_key'    => ['nullable', 'string', 'max:400'],
            'whatify_base_url'   => ['nullable', 'url', 'max:255'],
            'whatify_account_id' => ['nullable', 'string', 'max:80'],
            'whatify_from_number'=> ['nullable', 'string', 'max:30'],
        ]);

        if (empty($data['whatify_api_key']) || str_contains((string) $data['whatify_api_key'], '•')) {
            unset($data['whatify_api_key']);
        }

        PlatformSetting::current()->update([
            'whatify_enabled'    => (bool) ($request->input('whatify_enabled')),
            'whatify_base_url'   => $data['whatify_base_url'] ?? 'https://app.whatify.in',
            'whatify_account_id' => $data['whatify_account_id'] ?? null,
            'whatify_from_number'=> $data['whatify_from_number'] ?? null,
            'whatify_last_test_status' => null,
        ] + (isset($data['whatify_api_key']) ? ['whatify_api_key' => $data['whatify_api_key']] : []));

        return back()->with('status', 'Whatify (WhatsApp) settings saved.');
    }

    public function testWhatify(\App\Support\WhatifyService $whatify)
    {
        $this->ensureMigrated();
        $ping = $whatify->ping();
        PlatformSetting::current()->update([
            'whatify_last_tested_at'   => now(),
            'whatify_last_test_status' => $ping['ok'] ? 'ok' : 'error',
        ]);
        return back()->with(
            $ping['ok'] ? 'status' : 'error',
            $ping['ok'] ? '✓ Whatify reachable — health endpoint responded '.$ping['status'] : '❌ '.($ping['error'] ?? 'HTTP '.$ping['status'])
        );
    }

    /* ─────────────────────────── VAPID (push) ─────────────────────────── */

    public function updateVapid(Request $request)
    {
        $this->ensureMigrated();

        $data = $request->validate([
            'vapid_public_key'  => ['nullable', 'string', 'max:200'],
            'vapid_private_key' => ['nullable', 'string', 'max:400'],
            'vapid_subject'     => ['nullable', 'string', 'max:120'],
        ]);
        if (empty($data['vapid_private_key']) || str_contains((string) $data['vapid_private_key'], '•')) {
            unset($data['vapid_private_key']);
        }
        PlatformSetting::current()->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('status', 'VAPID keys saved. Users can now enable push notifications from the notifications page.');
    }

    /* ─────────────────────────── FEATURE FLAGS ─────────────────────────── */

    public function updateFeatures(Request $request)
    {
        $this->ensureMigrated();

        $allowed = array_keys(PlatformSetting::features());
        $flags = [];
        foreach ($allowed as $key) {
            $flags[$key] = (bool) $request->input("features.$key");
        }

        PlatformSetting::current()->update(['features_json' => $flags]);

        return back()->with('status', 'Feature flags updated.');
    }

    protected function ensureMigrated(): void
    {
        if (! SchemaCheck::has('platform_settings') || ! \Illuminate\Support\Facades\Schema::hasColumn('platform_settings', 'mail_driver')) {
            abort(redirect()->back()->with('error', 'Run `php artisan migrate` first — mail/analytics/payment columns are missing.'));
        }
    }
}

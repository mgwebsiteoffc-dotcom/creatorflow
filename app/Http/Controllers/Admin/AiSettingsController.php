<?php

namespace App\Http\Controllers\Admin;

use App\Domains\AI\Providers\OpenAiProvider;
use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

class AiSettingsController extends Controller
{
    public function edit()
    {
        $schemaMissing = ! SchemaCheck::has('platform_settings');
        $columnsMissing = $schemaMissing || ! \Illuminate\Support\Facades\Schema::hasColumn('platform_settings', 'ai_driver');

        $settings = $schemaMissing ? new PlatformSetting() : PlatformSetting::current();

        return view('admin.ai.edit', [
            'settings'       => $settings,
            'schemaMissing'  => $schemaMissing,
            'columnsMissing' => $columnsMissing,
        ]);
    }

    public function update(Request $request)
    {
        if (! SchemaCheck::has('platform_settings')) {
            return back()->with('error', 'platform_settings table not migrated. Run php artisan migrate first.');
        }

        $data = $request->validate([
            'ai_driver'                 => ['required', 'in:fake,openai'],
            'ai_openai_key'             => ['nullable', 'string', 'max:200'],
            'ai_openai_model'           => ['nullable', 'string', 'max:80'],
            'ai_openai_embedding_model' => ['nullable', 'string', 'max:80'],
            'ai_base_url'               => ['nullable', 'url', 'max:255'],
            'ai_temperature'            => ['nullable', 'numeric', 'min:0', 'max:2'],
        ]);

        $settings = PlatformSetting::current();

        // If the user leaves the key field blank (all bullets shown as placeholder),
        // keep the existing key.
        if (empty($data['ai_openai_key'])) {
            unset($data['ai_openai_key']);
        } elseif (str_contains((string) $data['ai_openai_key'], '•')) {
            unset($data['ai_openai_key']);
        }

        $settings->update(array_filter($data, fn ($v) => $v !== null));

        // Reset test status because config changed.
        $settings->update(['ai_last_test_status' => null, 'ai_last_tested_at' => null]);

        return back()->with('status', 'AI settings saved. Test the connection to verify.');
    }

    public function test(Request $request)
    {
        if (! SchemaCheck::has('platform_settings')) {
            return back()->with('error', 'platform_settings table not migrated.');
        }
        $settings = PlatformSetting::current();

        if ($settings->ai_driver !== 'openai') {
            $settings->update([
                'ai_last_test_status' => 'ok:fake',
                'ai_last_tested_at'   => now(),
            ]);
            return back()->with('status', '✓ Using offline (fake) AI — no key needed. Every request returns a deterministic mock.');
        }

        if (empty($settings->ai_openai_key)) {
            return back()->with('error', '❌ No OpenAI API key set. Save one first.');
        }

        try {
            $provider = new OpenAiProvider(
                (string) $settings->ai_openai_key,
                (string) ($settings->ai_openai_model ?: 'gpt-4o-mini'),
                (string) ($settings->ai_openai_embedding_model ?: 'text-embedding-3-small'),
                timeout: 20,
                baseUrl: $settings->ai_base_url,
            );
            $response = $provider->complete([
                ['role' => 'system', 'content' => 'Respond in one short sentence.'],
                ['role' => 'user',   'content' => 'Say "CreatorPlex AI is connected." and nothing else.'],
            ], ['temperature' => 0]);

            $ok = str_contains(strtolower($response->text), 'creatorplex');
            $settings->update([
                'ai_last_test_status' => $ok ? 'ok:openai' : 'partial',
                'ai_last_tested_at'   => now(),
            ]);
            return back()->with('status', '✓ Connected. AI returned: '.trim($response->text));
        } catch (\Throwable $e) {
            $settings->update([
                'ai_last_test_status' => 'error',
                'ai_last_tested_at'   => now(),
            ]);
            return back()->with('error', '❌ AI test failed: '.$e->getMessage());
        }
    }
}

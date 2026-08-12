<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        if (! \App\Support\SchemaCheck::has('platform_settings')) {
            // Return a placeholder object so the view can still render its form.
            $settings = new PlatformSetting([
                'paid_platform_fee_rate'   => 0.10,
                'barter_platform_fee_rate' => 0.05,
                'processing_markup_rate'   => 0.029,
                'processing_markup_fixed_cents' => 30,
                'escrow_hold_days'         => 7,
                'minimum_payout_cents'     => 1000,
                'require_creator_verification' => true,
                'allow_public_signup'      => true,
            ]);
            return view('admin.settings.edit', ['settings' => $settings, 'schemaMissing' => true]);
        }
        return view('admin.settings.edit', [
            'settings' => PlatformSetting::current(),
            'schemaMissing' => false,
        ]);
    }

    public function update(Request $request)
    {
        if (! \App\Support\SchemaCheck::has('platform_settings')) {
            return back()->with('error', 'Platform settings schema not migrated. Run `php artisan migrate` first.');
        }
        $data = $request->validate([
            'paid_platform_fee_rate'   => ['required', 'numeric', 'min:0', 'max:1'],
            'barter_platform_fee_rate' => ['required', 'numeric', 'min:0', 'max:1'],
            'processing_markup_rate'   => ['required', 'numeric', 'min:0', 'max:1'],
            'processing_markup_fixed'  => ['required', 'numeric', 'min:0'],
            'escrow_hold_days'         => ['required', 'integer', 'min:0', 'max:60'],
            'minimum_payout'           => ['required', 'numeric', 'min:0'],
            'require_creator_verification' => ['nullable', 'boolean'],
            'allow_public_signup'      => ['nullable', 'boolean'],
        ]);

        $data['processing_markup_fixed_cents'] = (int) round(((float) $data['processing_markup_fixed']) * 100);
        $data['minimum_payout_cents'] = (int) round(((float) $data['minimum_payout']) * 100);
        unset($data['processing_markup_fixed'], $data['minimum_payout']);

        $settings = PlatformSetting::current();
        $settings->update($data + [
            'require_creator_verification' => (bool) $request->input('require_creator_verification'),
            'allow_public_signup'          => (bool) $request->input('allow_public_signup'),
        ]);

        return back()->with('status', 'Platform settings saved.');
    }
}

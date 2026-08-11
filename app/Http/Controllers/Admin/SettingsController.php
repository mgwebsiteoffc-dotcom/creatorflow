<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', ['settings' => PlatformSetting::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'paid_platform_fee_rate'   => ['required', 'numeric', 'min:0', 'max:1'],
            'barter_platform_fee_rate' => ['required', 'numeric', 'min:0', 'max:1'],
            'processing_markup_rate'   => ['required', 'numeric', 'min:0', 'max:1'],
            'processing_markup_fixed_cents' => ['required', 'integer', 'min:0'],
            'escrow_hold_days'         => ['required', 'integer', 'min:0', 'max:60'],
            'minimum_payout_cents'     => ['required', 'integer', 'min:0'],
            'require_creator_verification' => ['nullable', 'boolean'],
            'allow_public_signup'      => ['nullable', 'boolean'],
        ]);

        $settings = PlatformSetting::current();
        $settings->update($data + [
            'require_creator_verification' => (bool) $request->input('require_creator_verification'),
            'allow_public_signup'          => (bool) $request->input('allow_public_signup'),
        ]);

        return back()->with('status', 'Platform settings saved.');
    }
}

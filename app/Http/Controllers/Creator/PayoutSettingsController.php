<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Support\RazorpayXService;
use Illuminate\Http\Request;

class PayoutSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $creator = $request->user()->creator;
        abort_unless($creator, 403);
        return view('creator.profile.payout', compact('creator'));
    }

    public function update(Request $request, RazorpayXService $rx)
    {
        $creator = $request->user()->creator;
        abort_unless($creator, 403);

        $data = $request->validate([
            'payout_method' => ['required', 'in:upi,bank'],
            'upi_vpa' => ['nullable', 'string', 'max:120', 'regex:/^[\w.\-]+@[\w.\-]+$/'],
            'bank_account_holder_name' => ['nullable', 'string', 'max:190'],
            'bank_account_number' => ['nullable', 'string', 'max:40'],
            'bank_ifsc' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'pan_number' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
        ]);

        // Reset provisioned ids when key details change so we re-provision next payout
        if ($data['payout_method'] !== $creator->payout_method
            || ($data['upi_vpa'] ?? null) !== $creator->upi_vpa
            || ($data['bank_account_number'] ?? null) !== $creator->bank_account_number) {
            $data['razorpayx_fund_account_id'] = null;
        }

        $creator->update($data + [
            'payout_method_status' => 'verified',
        ]);

        // Best-effort: pre-provision so first payout is instant
        try { $rx->ensureCreatorProvisioned($creator->fresh()); } catch (\Throwable) {}

        return back()->with('status', '✓ Payout details saved.');
    }
}

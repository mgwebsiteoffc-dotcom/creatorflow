<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * GET /contracts/{contract} — public view (creator can open from an email
     * link or from their assignment page).
     */
    public function show(Contract $contract)
    {
        return view('contracts.show', compact('contract'));
    }

    /**
     * POST /contracts/{contract}/sign — creator side.
     * Accepts either a typed name OR an SVG signature blob.
     */
    public function signAsCreator(Contract $contract, Request $request)
    {
        abort_unless(PlatformSetting::feature('contract_esign'), 404, 'Contract e-signature is disabled.');

        $user = $request->user();
        abort_unless($user && $user->creator && (int) $user->creator->id === (int) $contract->creator_id, 403);

        $data = $request->validate([
            'signature_name' => ['required', 'string', 'max:190'],
            'signature_svg'  => ['nullable', 'string', 'max:60000'],
            'agreed_text'    => ['required', 'accepted'],
        ]);

        $contract->update([
            'status'                  => $contract->signed_by_brand_at ? 'signed' : 'sent',
            'signed_by_creator_at'    => now(),
            'signature_ip'            => $request->ip(),
            'creator_signature_name'  => $data['signature_name'],
            'creator_signature_svg'   => $data['signature_svg'] ?? null,
            'creator_agreed_text'     => 'I agree to the terms above on '.now()->format('Y-m-d H:i').' from IP '.$request->ip(),
        ]);

        return redirect()->route('contracts.show', $contract)->with('status', '✓ Signed. Waiting for brand countersignature.');
    }

    /**
     * POST /contracts/{contract}/countersign — brand side (must be workspace member).
     */
    public function countersignAsBrand(Contract $contract, Request $request)
    {
        abort_unless(PlatformSetting::feature('contract_esign'), 404);

        $user = $request->user();
        $isMember = $user && $user->workspaces->contains('id', $contract->workspace_id);
        abort_unless($isMember, 403);

        $data = $request->validate([
            'signature_name' => ['required', 'string', 'max:190'],
            'signature_svg'  => ['nullable', 'string', 'max:60000'],
        ]);

        $contract->update([
            'status'                => 'signed',
            'signed_by_brand_at'    => now(),
            'brand_signature_ip'    => $request->ip(),
            'brand_signature_name'  => $data['signature_name'],
            'brand_signature_svg'   => $data['signature_svg'] ?? null,
        ]);

        return back()->with('status', '✓ Countersigned. Contract executed.');
    }
}

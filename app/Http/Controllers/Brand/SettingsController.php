<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function profile(TenantContext $tenant)
    {
        return view('brand.settings.profile', [
            'workspace'  => $tenant->active(),
            'currencies' => Workspace::supportedCurrencies(),
        ]);
    }

    public function updateProfile(Request $request, TenantContext $tenant)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:190'],
            'legal_name'    => ['nullable', 'string', 'max:190'],
            'website'       => ['nullable', 'url', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'country'       => ['nullable', 'string', 'max:2'],
            'currency'      => ['required', 'string', 'in:'.implode(',', array_keys(Workspace::supportedCurrencies()))],
            'timezone'      => ['nullable', 'string', 'max:60'],
            'address_line1' => ['nullable', 'string', 'max:190'],
            'address_line2' => ['nullable', 'string', 'max:190'],
            'address_city'  => ['nullable', 'string', 'max:120'],
            'address_state' => ['nullable', 'string', 'max:120'],
            'address_postal'=> ['nullable', 'string', 'max:30'],
            'tax_type'      => ['nullable', 'in:gstin,vat,ein,none'],
            'tax_id'        => ['nullable', 'string', 'max:60'],
            'billing_notes' => ['nullable', 'string', 'max:2000'],
            'logo'          => ['nullable', 'image', 'max:2048'],
        ]);

        $workspace = $tenant->active();

        if ($request->hasFile('logo')) {
            $data['logo_path'] = Storage::disk('public')->url(
                $request->file('logo')->store('workspace-logos', 'public')
            );
        }
        unset($data['logo']);

        // Uppercase currency + country
        $data['currency'] = strtoupper($data['currency']);
        if (! empty($data['country'])) $data['country'] = strtoupper($data['country']);

        $workspace->update($data);

        return back()->with('status', 'Brand profile saved.');
    }

    public function team(TenantContext $tenant)
    {
        $workspace = $tenant->active()->load(['users']);
        return view('brand.settings.team', compact('workspace'));
    }
}

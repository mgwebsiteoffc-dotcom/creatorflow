<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\ReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index()
    {
        abort_unless(PlatformSetting::feature('referrals'), 404, 'Referrals are disabled. Turn on in Integrations → Feature flags.');
        $codes = ReferralCode::query()->latest()->paginate(30);
        $stats = [
            'total_codes'      => ReferralCode::count(),
            'active_codes'     => ReferralCode::where('status', 'active')->count(),
            'total_signups'    => (int) ReferralCode::sum('signup_count'),
            'total_conversions'=> (int) ReferralCode::sum('conversion_count'),
            'total_commission' => (int) ReferralCode::sum('lifetime_commission_cents'),
        ];
        return view('admin.referrals.index', compact('codes', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_type' => ['required', 'in:creator,user,agency'],
            'owner_id'   => ['required', 'integer'],
            'code'       => ['nullable', 'string', 'max:40', 'regex:/^[A-Z0-9\-]+$/'],
            'label'      => ['nullable', 'string', 'max:190'],
            'kind'       => ['required', 'in:brand_referral,creator_affiliate,partner'],
            'commission_rate'        => ['nullable', 'numeric', 'min:0', 'max:100'],
            'commission_fixed_cents' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['uuid'] = (string) Str::uuid();
        $data['code'] = $data['code'] ?: strtoupper(Str::random(8));
        ReferralCode::create($data);
        return back()->with('status', "Code {$data['code']} created.");
    }

    public function toggle(ReferralCode $code)
    {
        $code->update(['status' => $code->status === 'active' ? 'disabled' : 'active']);
        return back();
    }

    public function destroy(ReferralCode $code) { $code->delete(); return back()->with('status', 'Code deleted.'); }
}

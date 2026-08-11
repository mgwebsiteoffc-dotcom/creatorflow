<?php

namespace App\Http\Controllers\Brand;

use App\Domains\Campaigns\Actions\AcceptInvitation;
use App\Domains\Matching\GenerateMatches;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignCreatorMatch;
use App\Models\CampaignInvitation;
use App\Models\Creator;
use App\Support\CreatorTaxonomy;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreatorMarketplaceController extends Controller
{
    public function index(Request $request, TenantContext $tenant)
    {
        $mode      = $request->get('mode', 'city'); // 'city' | 'tier'
        $cities    = array_filter((array) $request->input('cities', []));
        $tiers     = array_filter((array) $request->input('tiers', []));
        $genders   = array_filter((array) $request->input('genders', []));
        $ages      = array_filter((array) $request->input('ages', []));
        $languages = array_filter((array) $request->input('languages', []));

        $creators = Creator::active()
            ->with(['nicheRows', 'socialAccounts'])
            ->when($request->get('niche'), fn ($q, $niche) => $q->whereHas('nicheRows', fn ($q2) => $q2->where('niche', $niche)))
            ->when($request->integer('min_followers'), fn ($q, $min) => $q->where('follower_count_total', '>=', $min))
            ->when($request->integer('max_followers'), fn ($q, $max) => $q->where('follower_count_total', '<=', $max))
            ->when($request->input('min_engagement'), fn ($q, $min) => $q->where('engagement_rate', '>=', (float) $min))
            ->when($request->boolean('barter'), fn ($q) => $q->where('accepts_barter', true))
            ->when($request->boolean('paid'), fn ($q) => $q->where('accepts_paid', true))
            ->when($request->get('q'), fn ($q, $s) => $q->where(function ($qq) use ($s) {
                $qq->where('display_name', 'like', "%{$s}%")
                   ->orWhere('bio', 'like', "%{$s}%")
                   ->orWhere('city', 'like', "%{$s}%");
            }))
            ->inCities($cities)
            ->inTiers($tiers)
            ->genderIn($genders)
            ->ageIn($ages)
            ->languageIn($languages)
            ->orderByDesc('performance_score')
            ->paginate(24)
            ->withQueryString();

        $niches      = \DB::table('creator_niches')->distinct()->pluck('niche')->sort()->values();
        $cityOptions = CreatorTaxonomy::cityOptions();
        $tierOptions = CreatorTaxonomy::tiers();
        $genderOpts  = CreatorTaxonomy::genders();
        $ageOpts     = CreatorTaxonomy::ageRanges();
        $langOpts    = CreatorTaxonomy::languages();

        // For "Invite to campaign" quick action.
        $activeCampaigns = $tenant->active()->campaigns()
            ->whereIn('status', ['draft', 'matching', 'inviting', 'active'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['id', 'title', 'status']);

        return view('brand.creators.index', compact(
            'creators', 'niches', 'mode',
            'cityOptions', 'tierOptions', 'genderOpts', 'ageOpts', 'langOpts',
            'cities', 'tiers', 'genders', 'ages', 'languages',
            'activeCampaigns'
        ));
    }

    public function show(Creator $creator)
    {
        $creator->load(['nicheRows', 'socialAccounts', 'portfolioItems', 'preferences']);

        return view('brand.creators.show', compact('creator'));
    }

    public function invite(Request $request, TenantContext $tenant)
    {
        $data = $request->validate([
            'creator_id' => ['required', 'exists:creators,id'],
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'message' => ['nullable', 'string'],
        ]);

        $campaign = Campaign::findOrFail($data['campaign_id']);
        abort_unless($campaign->workspace_id === $tenant->id(), 403);

        $invitation = CampaignInvitation::create([
            'campaign_id' => $campaign->id,
            'creator_id' => $data['creator_id'],
            'channel' => 'in_app',
            'message' => $data['message'],
            'status' => 'sent',
            'sent_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        CampaignCreatorMatch::updateOrCreate(
            ['campaign_id' => $campaign->id, 'creator_id' => $data['creator_id']],
            ['status' => 'invited', 'invited_at' => now(), 'score' => 70]
        );

        return back()->with('status', 'Invitation sent.');
    }

    public function acceptOnCreatorBehalf(CampaignInvitation $invitation, AcceptInvitation $accept, TenantContext $tenant)
    {
        abort_unless($invitation->campaign->workspace_id === $tenant->id(), 403);

        $accept->handle($invitation);

        return back()->with('status', 'Creator added and order created.');
    }
}

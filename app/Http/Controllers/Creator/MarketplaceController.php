<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignCreatorMatch;
use App\Models\Application;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $creator = $request->user()->creator;
        $creatorNiches = $creator->nicheRows->pluck('niche')->all();

        $campaigns = Campaign::whereIn('status', ['inviting', 'active'])
            ->with(['workspace', 'products.product'])
            ->when($request->get('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->get('niche'), fn ($q, $niche) => $q->where('niche', $niche))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('creator.marketplace', compact('campaigns', 'creatorNiches'));
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['workspace', 'products.product.primaryImage', 'products.variant']);

        return view('creator.campaign-show', compact('campaign'));
    }

    public function apply(Request $request, Campaign $campaign)
    {
        $creator = $request->user()->creator;

        $data = $request->validate([
            'cover_note' => ['nullable', 'string', 'max:2000'],
            'proposed_fee_cents' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($campaign->applications()->where('creator_id', $creator->id)->exists()) {
            return back()->with('error', 'You already applied to this campaign.');
        }

        Application::create([
            'campaign_id' => $campaign->id,
            'creator_id' => $creator->id,
            'cover_note' => $data['cover_note'] ?? null,
            'proposed_fee_cents' => $data['proposed_fee_cents'] ?? null,
            'status' => 'submitted',
        ]);

        return redirect()->route('creator.marketplace')->with('status', 'Application sent! We\'ll notify you when the brand responds.');
    }
}

<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Campaign;
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

        // Which of these campaigns has the creator already applied to?
        $appliedIds = $creator->applications()
            ->whereIn('campaign_id', $campaigns->pluck('id'))
            ->pluck('campaign_id')
            ->all();

        return view('creator.marketplace', compact('campaigns', 'creatorNiches', 'appliedIds'));
    }

    public function show(Campaign $campaign, Request $request)
    {
        $campaign->load(['workspace', 'products.product.primaryImage', 'products.variant']);
        $creator = $request->user()->creator;
        $existingApplication = $creator
            ? $campaign->applications()->where('creator_id', $creator->id)->first()
            : null;

        return view('creator.campaign-show', compact('campaign', 'existingApplication'));
    }

    public function apply(Request $request, Campaign $campaign)
    {
        $creator = $request->user()->creator;

        $data = $request->validate([
            'cover_note' => ['nullable', 'string', 'max:2000'],
            'proposed_fee' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($campaign->applications()->where('creator_id', $creator->id)->exists()) {
            return back()->with('error', 'You already applied to this campaign.');
        }

        Application::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'campaign_id' => $campaign->id,
            'creator_id' => $creator->id,
            'cover_note' => $data['cover_note'] ?? null,
            'proposed_fee_cents' => isset($data['proposed_fee']) ? (int) round(((float) $data['proposed_fee']) * 100) : null,
            'status' => 'submitted',
        ]);

        // Confirmation to creator
        \App\Support\NotifyEvent::fire('creator.application.received', $creator, [
            'creator_name'   => $creator->display_name,
            'brand_name'     => $campaign->workspace->name,
            'campaign_title' => $campaign->title,
            'link'           => route('creator.applications'),
        ]);

        // Notify every user in the campaign's workspace
        foreach ($campaign->workspace?->users ?? [] as $brandUser) {
            \App\Support\NotifyEvent::fire('brand.application.received', $brandUser, [
                'brand_name'      => $campaign->workspace->name,
                'creator_name'    => $creator->display_name,
                'campaign_title'  => $campaign->title,
                'followers'       => number_format((int) $creator->follower_count_total),
                'engagement_rate' => $creator->engagement_rate,
                'link'            => route('brand.applications.index'),
            ]);
        }

        return redirect()
            ->route('creator.applications')
            ->with('status', 'Application sent! Track its status below.');
    }

    public function applications(Request $request)
    {
        $creator = $request->user()->creator;

        $applications = $creator->applications()
            ->with(['campaign.workspace', 'campaign.products.product'])
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'all'         => $creator->applications()->count(),
            'submitted'   => $creator->applications()->where('status', 'submitted')->count(),
            'shortlisted' => $creator->applications()->where('status', 'shortlisted')->count(),
            'approved'    => $creator->applications()->where('status', 'approved')->count(),
            'rejected'    => $creator->applications()->where('status', 'rejected')->count(),
        ];

        return view('creator.applications', compact('applications', 'counts'));
    }

    public function withdrawApplication(Application $application, Request $request)
    {
        abort_unless($application->creator_id === $request->user()->creator->id, 403);

        if (in_array($application->status, ['approved', 'rejected', 'withdrawn'], true)) {
            return back()->with('error', 'This application can no longer be withdrawn.');
        }

        $application->update([
            'status' => 'withdrawn',
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Application withdrawn.');
    }
}

<?php

namespace App\Http\Controllers\Brand;

use App\Domains\AI\Actions\GenerateCampaignSuggestion;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, TenantContext $tenant, GenerateCampaignSuggestion $suggest)
    {
        $workspace = $tenant->active();

        $stats = [
            'products' => $workspace->products()->count(),
            'active_campaigns' => $workspace->campaigns()->active()->count(),
            'creators_reached' => $workspace->campaigns()->withCount('assignments')->get()->sum('assignments_count'),
            'attributed_revenue_cents' => (int) \App\Models\Attribution::where('workspace_id', $workspace->id)->sum('revenue_cents'),
            'pending_content' => \App\Models\ContentSubmission::whereIn(
                'campaign_id',
                $workspace->campaigns()->pluck('id')
            )->whereIn('status', ['submitted', 'in_review'])->count(),
            'unread_messages' => 0,
        ];

        $suggestion = $workspace->products()->exists()
            ? $suggest->run($workspace)
            : null;

        $recentCampaigns = $workspace->campaigns()
            ->withCount(['assignments', 'products'])
            ->latest()
            ->take(6)
            ->get();

        return view('brand.dashboard', compact('workspace', 'stats', 'suggestion', 'recentCampaigns'));
    }
}

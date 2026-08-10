<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Attribution;
use App\Models\Campaign;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(TenantContext $tenant, Request $request)
    {
        $workspace = $tenant->active();

        $campaignId = $request->integer('campaign_id');
        $campaigns = $workspace->campaigns()->orderBy('title')->get(['id', 'title']);

        $base = Attribution::where('workspace_id', $workspace->id);
        if ($campaignId) {
            $base->where('campaign_id', $campaignId);
        }

        $attributedRevenueCents = (int) (clone $base)->sum('revenue_cents');
        $attributedOrders = (clone $base)->count();

        $creatorLeaderboard = (clone $base)
            ->select('creator_id', DB::raw('SUM(revenue_cents) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('creator_id')
            ->orderByDesc('revenue')
            ->with('creator')
            ->limit(10)
            ->get();

        $daily = DB::table('analytics_daily_campaign')
            ->where('workspace_id', $workspace->id)
            ->when($campaignId, fn ($q) => $q->where('campaign_id', $campaignId))
            ->select('date', DB::raw('SUM(orders) as orders'), DB::raw('SUM(revenue_cents) as revenue_cents'), DB::raw('SUM(content_count) as content_count'))
            ->groupBy('date')
            ->orderBy('date')
            ->limit(60)
            ->get();

        return view('brand.analytics.index', compact(
            'campaigns',
            'campaignId',
            'attributedRevenueCents',
            'attributedOrders',
            'creatorLeaderboard',
            'daily',
        ));
    }
}

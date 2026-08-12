<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Attribution;
use App\Models\Campaign;
use App\Models\ContentSubmission;
use App\Support\SchemaCheck;
use App\Support\TenantContext;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(TenantContext $tenant, Request $request)
    {
        $workspace  = $tenant->active();
        $campaignId = $request->integer('campaign_id') ?: null;
        $days       = (int) min(180, max(7, $request->integer('days') ?: 30));

        $campaigns = $workspace->campaigns()
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'status', 'created_at']);

        $from = now()->subDays($days - 1)->startOfDay();

        $base = Attribution::where('workspace_id', $workspace->id)
            ->where('attributed_at', '>=', $from);
        if ($campaignId) {
            $base->where('campaign_id', $campaignId);
        }

        // KPI band ---------------------------------------------------------
        $totalRevenueCents = (int) (clone $base)->sum('revenue_cents');
        $totalOrders       = (int) (clone $base)->count();
        $activeCreators    = (int) (clone $base)->distinct('creator_id')->count('creator_id');
        $aov               = $totalOrders > 0 ? intdiv($totalRevenueCents, $totalOrders) : 0;

        // Previous period comparison for deltas ----------------------------
        $prevFrom = (clone $from)->subDays($days);
        $prevTo   = (clone $from)->subSecond();
        $prevBase = Attribution::where('workspace_id', $workspace->id)
            ->whereBetween('attributed_at', [$prevFrom, $prevTo]);
        if ($campaignId) {
            $prevBase->where('campaign_id', $campaignId);
        }
        $prevRevenueCents = (int) $prevBase->sum('revenue_cents');
        $prevOrders       = (int) (clone $prevBase)->count();

        $deltaRevenuePct = $prevRevenueCents > 0
            ? round((($totalRevenueCents - $prevRevenueCents) / $prevRevenueCents) * 100, 1)
            : ($totalRevenueCents > 0 ? 100.0 : 0.0);
        $deltaOrdersPct = $prevOrders > 0
            ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1)
            : ($totalOrders > 0 ? 100.0 : 0.0);

        // Daily series (fill missing days with zeros) ----------------------
        $rawDaily = (clone $base)
            ->select(
                DB::raw('DATE(attributed_at) as date'),
                DB::raw('SUM(revenue_cents) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $daily = collect();
        foreach (CarbonPeriod::create($from, now()->endOfDay()) as $day) {
            $key = $day->format('Y-m-d');
            $row = $rawDaily->get($key);
            $daily->push((object) [
                'date'    => $day,
                'revenue' => (int) ($row->revenue ?? 0),
                'orders'  => (int) ($row->orders  ?? 0),
            ]);
        }

        // Top creators (leaderboard) ---------------------------------------
        $creatorLeaderboard = (clone $base)
            ->select(
                'creator_id',
                DB::raw('SUM(revenue_cents) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('creator_id')
            ->orderByDesc('revenue')
            ->with('creator:id,uuid,display_name,slug,city,follower_count_total,engagement_rate')
            ->limit(10)
            ->get();

        // ROAS per campaign (revenue / brand cost) -------------------------
        // Cost = commission + product_cost + fee for the same window,
        // pulled from the analytics_daily_campaign rollup.
        $campaignRoas = collect();
        if (SchemaCheck::has('analytics_daily_campaign')) {
            $campaignRoas = DB::table('analytics_daily_campaign')
                ->where('workspace_id', $workspace->id)
                ->where('date', '>=', $from->toDateString())
                ->when($campaignId, fn ($q) => $q->where('campaign_id', $campaignId))
                ->select(
                    'campaign_id',
                    DB::raw('SUM(revenue_cents) as revenue'),
                    DB::raw('SUM(commission_cents + product_cost_cents + fee_cents) as cost'),
                    DB::raw('SUM(orders) as orders'),
                    DB::raw('SUM(content_count) as content_count')
                )
                ->groupBy('campaign_id')
                ->orderByDesc('revenue')
                ->get()
                ->map(function ($r) use ($campaigns) {
                    $r->campaign = $campaigns->firstWhere('id', $r->campaign_id);
                    $r->roas     = $r->cost > 0 ? round($r->revenue / $r->cost, 2) : null;
                    return $r;
                });
        }

        // Top content (submissions ordered by attributed revenue) ----------
        $topContent = collect();
        if (SchemaCheck::has('content_submissions')) {
            $topContent = ContentSubmission::query()
                ->whereHas('assignment.campaign', fn ($q) => $q->where('workspace_id', $workspace->id)
                    ->when($campaignId, fn ($qq) => $qq->where('id', $campaignId)))
                ->where('status', 'approved')
                ->with(['assignment.creator:id,uuid,display_name,slug', 'assignment.campaign:id,uuid,title'])
                ->orderByDesc('submitted_at')
                ->limit(6)
                ->get();
        }

        return view('brand.analytics.index', compact(
            'campaigns',
            'campaignId',
            'days',
            'totalRevenueCents',
            'totalOrders',
            'activeCreators',
            'aov',
            'deltaRevenuePct',
            'deltaOrdersPct',
            'daily',
            'creatorLeaderboard',
            'campaignRoas',
            'topContent',
        ));
    }
}

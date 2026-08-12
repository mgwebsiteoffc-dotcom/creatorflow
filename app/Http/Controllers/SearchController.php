<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Creator;
use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Cmd-K global search — returns results scoped to the current user's context.
 *
 *  - Brand user  → their workspace campaigns + products + creators (public)
 *  - Creator     → open campaigns + their assignments
 *  - Admin       → also users + workspaces (superset)
 *  - Guest       → nothing (route is auth-protected)
 *
 * Also includes a hard-coded "quick actions" list so the palette doubles
 * as a keyboard-driven jump-to-page.
 */
class SearchController extends Controller
{
    public function query(Request $request, TenantContext $tenant): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'actions' => $this->quickActions()]);
        }

        $user = Auth::user();
        $results = [];

        // Campaigns (in the active workspace)
        try {
            $workspace = $tenant->active();
            $campaigns = Campaign::where('workspace_id', $workspace->id)
                ->where('title', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'uuid', 'title', 'status']);
            foreach ($campaigns as $c) {
                $results[] = [
                    'group' => 'Campaigns',
                    'title' => $c->title,
                    'meta'  => ucfirst($c->status ?: 'draft'),
                    'url'   => route('brand.campaigns.show', $c),
                    'icon'  => 'campaigns',
                ];
            }

            $products = Product::where('workspace_id', $workspace->id)
                ->where('title', 'like', "%{$q}%")
                ->limit(5)
                ->get(['id', 'uuid', 'title']);
            foreach ($products as $p) {
                $results[] = [
                    'group' => 'Products',
                    'title' => $p->title,
                    'meta'  => 'View',
                    'url'   => route('brand.products.show', $p),
                    'icon'  => 'products',
                ];
            }
        } catch (\Throwable) {
            // Not in a brand workspace — ignore silently.
        }

        // Creators (marketplace-visible)
        $creators = Creator::where('status', 'active')
            ->where(fn ($qq) => $qq->where('display_name', 'like', "%{$q}%")
                                   ->orWhere('city', 'like', "%{$q}%"))
            ->limit(5)
            ->get(['id', 'uuid', 'slug', 'display_name', 'city', 'follower_count_total']);
        foreach ($creators as $cr) {
            $results[] = [
                'group' => 'Creators',
                'title' => $cr->display_name,
                'meta'  => trim(($cr->city ?: '—').' · '.number_format($cr->follower_count_total).' followers'),
                'url'   => $user ? route('brand.creators.show', $cr) : url('/creators/'.$cr->slug),
                'icon'  => 'creators',
            ];
        }

        return response()->json([
            'results' => $results,
            'actions' => $this->quickActions(),
        ]);
    }

    /** Static "jump to page" list. */
    protected function quickActions(): array
    {
        $user = Auth::user();
        if (! $user) return [];

        $actions = [];

        if ($user->creator) {
            $actions = [
                ['title' => 'Marketplace',     'url' => route('creator.marketplace'),       'icon' => 'marketplace', 'shortcut' => 'G M'],
                ['title' => 'My applications', 'url' => route('creator.applications'),      'icon' => 'applications','shortcut' => 'G A'],
                ['title' => 'My work',         'url' => route('creator.assignments.index'), 'icon' => 'work',        'shortcut' => 'G W'],
                ['title' => 'Earnings',        'url' => route('creator.earnings.index'),    'icon' => 'earnings',    'shortcut' => 'G E'],
                ['title' => 'Public profile',  'url' => route('creator.profile.show'),      'icon' => 'profile',     'shortcut' => 'G P'],
            ];
        } else {
            $actions = [
                ['title' => 'New campaign',   'url' => route('brand.campaigns.create'),  'icon' => 'plus',        'shortcut' => 'C N'],
                ['title' => 'Campaigns',      'url' => route('brand.campaigns.index'),   'icon' => 'campaigns',   'shortcut' => 'G C'],
                ['title' => 'Creators',       'url' => route('brand.creators.index'),    'icon' => 'creators',    'shortcut' => 'G R'],
                ['title' => 'Orders',         'url' => route('brand.orders.index'),      'icon' => 'orders',      'shortcut' => 'G O'],
                ['title' => 'Analytics',      'url' => route('brand.analytics'),         'icon' => 'analytics',   'shortcut' => 'G A'],
                ['title' => 'Add funds',      'url' => route('brand.escrow.top-up'),     'icon' => 'billing',     'shortcut' => 'G $'],
            ];
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            $actions[] = ['title' => 'Admin dashboard', 'url' => route('admin.dashboard'), 'icon' => 'admin', 'shortcut' => 'G !'];
        }

        return $actions;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\PlatformSetting;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

class CreatorPortfolioController extends Controller
{
    /**
     * Directory of all active creators — SEO landing at /creators.
     */
    public function index(Request $request)
    {
        abort_unless(PlatformSetting::feature('public_creator_pages'), 404);

        $creators = Creator::active()
            ->with(['socialAccounts', 'nicheRows'])
            ->when($request->get('city'), fn ($q, $city) => $q->where('city', 'like', "%{$city}%"))
            ->when($request->get('niche'), fn ($q, $n) => $q->whereHas('nicheRows', fn ($qq) => $qq->where('niche', $n)))
            ->when($request->get('tier'), function ($q, $t) {
                $ranges = \App\Support\CreatorTaxonomy::tiers()[$t] ?? null;
                if (! $ranges) return $q;
                if (\Illuminate\Support\Facades\Schema::hasColumn('creators', 'tier')) return $q->where('tier', $t);
                return $q->where('follower_count_total', '>=', $ranges['min'])
                    ->when($ranges['max'], fn ($qq) => $qq->where('follower_count_total', '<', $ranges['max']));
            })
            ->orderByDesc('performance_score')
            ->paginate(30)
            ->withQueryString();

        return view('marketing.creators.index', compact('creators'));
    }

    public function show(string $slug)
    {
        abort_unless(PlatformSetting::feature('public_creator_pages'), 404);

        $creator = Creator::where('slug', $slug)
            ->where('status', 'active')
            ->with(['socialAccounts', 'portfolioItems', 'nicheRows'])
            ->firstOrFail();

        return view('marketing.creators.show', compact('creator'));
    }
}

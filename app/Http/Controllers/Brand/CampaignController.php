<?php

namespace App\Http\Controllers\Brand;

use App\Domains\Campaigns\Actions\CreateCampaign;
use App\Domains\Campaigns\Actions\LaunchCampaign;
use App\Domains\AI\Actions\GenerateCampaignSuggestion;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignCreatorMatch;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(TenantContext $tenant)
    {
        $campaigns = $tenant->active()->campaigns()
            ->withCount(['assignments', 'products', 'matches'])
            ->latest()
            ->paginate(20);

        return view('brand.campaigns.index', compact('campaigns'));
    }

    public function create(TenantContext $tenant, GenerateCampaignSuggestion $suggest)
    {
        $workspace = $tenant->active();
        $products = $workspace->products()->with('variants')->orderBy('hero_score', 'desc')->get();
        $suggestion = $products->isNotEmpty() ? $suggest->run($workspace) : null;

        return view('brand.campaigns.create', compact('products', 'suggestion'));
    }

    public function store(Request $request, TenantContext $tenant, CreateCampaign $create)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:barter,paid,affiliate,hybrid'],
            'niche' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string'],
            'brief' => ['nullable', 'string'],
            'budget_total_cents' => ['nullable', 'integer', 'min:0'],
            'creator_fee_cents' => ['nullable', 'integer', 'min:0'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'acceptance_rate_assumed' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.variant_id' => ['nullable', 'exists:product_variants,id'],
            'products.*.target_creators' => ['required', 'integer', 'min:1'],
            'products.*.fee_cents' => ['nullable', 'integer', 'min:0'],
        ]);

        $workspace = $tenant->active();

        $campaign = $create->handle(
            $workspace,
            $data,
            $data['products'],
            $request->user()->id,
        );

        return redirect()->route('brand.campaigns.show', $campaign)
            ->with('status', 'Campaign draft created. Review matches and launch when ready.');
    }

    public function show(Campaign $campaign, TenantContext $tenant)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);

        $campaign->load([
            'products.product.primaryImage', 'products.variant',
            'matches.creator.nicheRows',
            'assignments.creator', 'assignments.order', 'assignments.submissions',
            'invitations.creator',
            'applications.creator.socialAccounts',
            'applications.creator.nicheRows',
            'references.uploader',
        ]);

        $pendingApplications = $campaign->applications
            ->whereIn('status', ['submitted', 'shortlisted'])
            ->sortByDesc('created_at')
            ->values();

        $funnel = [
            'invited' => $campaign->invitations->count(),
            'accepted' => $campaign->assignments->count(),
            'shipped' => $campaign->assignments->filter(fn ($a) => in_array($a->status, ['shipped', 'delivered', 'in_progress', 'submitted', 'approved', 'completed']))->count(),
            'content' => $campaign->assignments->sum(fn ($a) => $a->submissions->count()),
            'approved' => $campaign->assignments->whereIn('status', ['approved', 'completed'])->count(),
        ];

        return view('brand.campaigns.show', compact('campaign', 'funnel', 'pendingApplications'));
    }

    public function launch(Campaign $campaign, TenantContext $tenant, LaunchCampaign $launch)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);

        $launch->handle($campaign);

        return back()->with('status', 'Campaign launched! Invitations are going out now.');
    }

    public function matches(Campaign $campaign, TenantContext $tenant)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);

        $matches = $campaign->matches()
            ->with('creator.nicheRows', 'creator.socialAccounts')
            ->orderByDesc('score')
            ->paginate(50);

        return view('brand.campaigns.matches', compact('campaign', 'matches'));
    }

    protected function authorizeWorkspace(int $workspaceId, TenantContext $tenant): void
    {
        abort_unless($tenant->id() === $workspaceId, 403);
    }
}

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

        $eager = [
            'products.product.primaryImage', 'products.variant',
            'matches.creator.nicheRows',
            'assignments.creator', 'assignments.order', 'assignments.submissions',
            'invitations.creator',
            'applications.creator.socialAccounts',
            'applications.creator.nicheRows',
        ];
        if (\App\Support\SchemaCheck::has('campaign_references')) {
            $eager[] = 'references.uploader';
        }
        $campaign->load($eager);

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

        // Rich per-campaign stats
        $completedWorks = $campaign->assignments->whereIn('status', ['approved', 'completed'])->count();
        $inProgress     = $campaign->assignments->whereIn('status', ['contract_sent', 'contract_signed', 'order_created', 'shipped', 'delivered', 'in_progress'])->count();
        $needsReview    = $campaign->assignments->whereIn('status', ['submitted', 'changes_requested'])->count();
        $applicationsCount = $campaign->applications->count();
        $approvedApplications = $campaign->applications->where('status', 'approved')->count();
        $rejectedApplications = $campaign->applications->where('status', 'rejected')->count();

        // Attribution
        $attributedRevenue = (int) \App\Models\Attribution::where('campaign_id', $campaign->id)->sum('revenue_cents');
        $attributedOrders  = (int) \App\Models\Attribution::where('campaign_id', $campaign->id)->count();

        // Spend
        $feesPaidCents = (int) \App\Models\Payout::whereIn('assignment_id', $campaign->assignments->pluck('id'))
            ->where('status', 'paid')->sum('net_cents');
        $feesPendingCents = (int) \App\Models\Payout::whereIn('assignment_id', $campaign->assignments->pluck('id'))
            ->where('status', 'pending')->sum('net_cents');
        $totalCostCents = $feesPaidCents + $feesPendingCents + ($campaign->product_cost_cents ?? 0);
        $roas = $totalCostCents > 0 ? round($attributedRevenue / $totalCostCents, 2) : null;

        $campaignStats = [
            'completed_works'       => $completedWorks,
            'in_progress'           => $inProgress,
            'needs_review'          => $needsReview,
            'applications_count'    => $applicationsCount,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            'attributed_revenue'    => $attributedRevenue,
            'attributed_orders'     => $attributedOrders,
            'fees_paid'             => $feesPaidCents,
            'fees_pending'          => $feesPendingCents,
            'total_cost'            => $totalCostCents,
            'roas'                  => $roas,
            'days_running'          => $campaign->launched_at ? now()->diffInDays($campaign->launched_at) : 0,
        ];

        // Timeline (recent activity)
        $timeline = collect();
        if ($campaign->launched_at) {
            $timeline->push(['at' => $campaign->launched_at, 'icon' => '🚀', 'title' => 'Campaign launched', 'body' => 'Invitations started going out.']);
        }
        $timeline->push(['at' => $campaign->created_at, 'icon' => '✨', 'title' => 'Campaign drafted', 'body' => 'Draft created by brand.']);
        foreach ($campaign->applications->sortByDesc('created_at')->take(5) as $app) {
            $timeline->push([
                'at' => $app->created_at, 'icon' => '📥',
                'title' => "{$app->creator->display_name} applied",
                'body' => "Application ".$app->status,
            ]);
        }
        foreach ($campaign->assignments->sortByDesc('created_at')->take(5) as $asg) {
            $timeline->push([
                'at' => $asg->created_at, 'icon' => '🤝',
                'title' => "{$asg->creator->display_name} accepted",
                'body' => 'Assignment · '.str_replace('_', ' ', $asg->status),
            ]);
            foreach ($asg->submissions as $sub) {
                $timeline->push([
                    'at' => $sub->submitted_at ?? $sub->created_at, 'icon' => '🎬',
                    'title' => "Content submitted by {$asg->creator->display_name}",
                    'body' => ucfirst($sub->type)." · ".str_replace('_', ' ', $sub->status),
                ]);
            }
        }
        $eventLogs = \App\Support\SchemaCheck::has('event_log')
            ? \App\Models\EventLog::where('aggregate_type', 'Campaign')
                ->where('aggregate_id', $campaign->id)
                ->latest('created_at')->take(10)->get()
            : collect();
        foreach ($eventLogs as $ev) {
            $timeline->push([
                'at' => $ev->created_at, 'icon' => '⚙️',
                'title' => str_replace('.', ' → ', $ev->event),
                'body' => 'System event',
            ]);
        }
        $timeline = $timeline->sortByDesc('at')->values()->take(20);

        return view('brand.campaigns.show', compact('campaign', 'funnel', 'pendingApplications', 'campaignStats', 'timeline'));
    }

    public function launch(Campaign $campaign, TenantContext $tenant, LaunchCampaign $launch)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);

        $launch->handle($campaign);

        return back()->with('status', 'Campaign launched! Invitations are going out now.');
    }

    public function pause(Campaign $campaign, TenantContext $tenant)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);
        $campaign->update(['status' => 'paused']);
        \App\Models\EventLog::record('campaign.paused', 'Campaign', $campaign->id, ['status' => 'paused']);
        return back()->with('status', 'Campaign paused. No new invitations will be sent.');
    }

    public function resume(Campaign $campaign, TenantContext $tenant)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);
        $newStatus = $campaign->launched_at ? 'active' : 'inviting';
        $campaign->update(['status' => $newStatus]);
        \App\Models\EventLog::record('campaign.resumed', 'Campaign', $campaign->id, ['status' => $newStatus]);
        return back()->with('status', 'Campaign resumed.');
    }

    public function end(Campaign $campaign, TenantContext $tenant)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);
        $campaign->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'end_date'     => $campaign->end_date ?: now(),
        ]);
        \App\Models\EventLog::record('campaign.completed', 'Campaign', $campaign->id, []);
        return back()->with('status', 'Campaign marked as completed.');
    }

    public function cancel(Campaign $campaign, TenantContext $tenant)
    {
        $this->authorizeWorkspace($campaign->workspace_id, $tenant);
        $campaign->update([
            'status'       => 'cancelled',
            'completed_at' => now(),
        ]);
        \App\Models\EventLog::record('campaign.cancelled', 'Campaign', $campaign->id, []);
        return back()->with('status', 'Campaign cancelled. Pending invitations expired.');
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

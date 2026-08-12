<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index(TenantContext $tenant, Request $request)
    {
        $status = $request->get('status', 'submitted');

        $applications = Application::whereIn(
            'campaign_id',
            $tenant->active()->campaigns()->pluck('id')
        )
            ->with([
                'creator.socialAccounts',
                'creator.nicheRows',
                'campaign',
            ])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'all' => Application::whereIn('campaign_id', $tenant->active()->campaigns()->pluck('id'))->count(),
            'submitted'   => Application::whereIn('campaign_id', $tenant->active()->campaigns()->pluck('id'))->where('status', 'submitted')->count(),
            'shortlisted' => Application::whereIn('campaign_id', $tenant->active()->campaigns()->pluck('id'))->where('status', 'shortlisted')->count(),
            'approved'    => Application::whereIn('campaign_id', $tenant->active()->campaigns()->pluck('id'))->where('status', 'approved')->count(),
            'rejected'    => Application::whereIn('campaign_id', $tenant->active()->campaigns()->pluck('id'))->where('status', 'rejected')->count(),
        ];

        return view('brand.applications.index', compact('applications', 'status', 'counts'));
    }

    public function shortlist(Application $application, Request $request, TenantContext $tenant)
    {
        $this->authorize($application, $tenant);

        $application->update([
            'status' => 'shortlisted',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Application shortlisted.');
    }

    public function approve(Application $application, Request $request, TenantContext $tenant)
    {
        $this->authorize($application, $tenant);

        $application->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Send the creator an in-app invitation so they can accept and enter the
        // normal fulfilment flow (contract → order → content → payout).
        $campaign = $application->campaign;
        $campaignProduct = $campaign->products()->orderBy('id')->first();

        CampaignInvitation::updateOrCreate(
            ['campaign_id' => $campaign->id, 'creator_id' => $application->creator_id],
            [
                'uuid' => (string) Str::uuid(),
                'campaign_product_id' => $campaignProduct?->id,
                'channel' => 'in_app',
                'message' => "Your application to \"{$campaign->title}\" was approved 🎉 — accept below to kick off the contract and order.",
                'status' => 'sent',
                'sent_at' => now(),
            ],
        );

        \App\Support\NotifyEvent::fire('creator.application.approved', $application->creator, [
            'creator_name'   => $application->creator->display_name,
            'brand_name'     => $campaign->workspace->name,
            'campaign_title' => $campaign->title,
            'link'           => url('/creator/invitations'),
        ]);

        return back()->with('status', 'Application approved. Creator was invited to accept.');
    }

    public function reject(Application $application, Request $request, TenantContext $tenant)
    {
        $this->authorize($application, $tenant);

        $application->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        \App\Support\NotifyEvent::fire('creator.application.rejected', $application->creator, [
            'creator_name'   => $application->creator->display_name,
            'brand_name'     => $application->campaign->workspace->name,
            'campaign_title' => $application->campaign->title,
            'link'           => url('/creator/marketplace'),
        ]);

        return back()->with('status', 'Application declined.');
    }

    protected function authorize(Application $application, TenantContext $tenant): void
    {
        abort_unless(
            $tenant->active()->campaigns()->whereKey($application->campaign_id)->exists(),
            403
        );
    }
}

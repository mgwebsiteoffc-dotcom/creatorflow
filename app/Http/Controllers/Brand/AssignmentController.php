<?php

namespace App\Http\Controllers\Brand;

use App\Domains\Content\Actions\ApproveContent;
use App\Http\Controllers\Controller;
use App\Models\CampaignAssignment;
use App\Models\ContentSubmission;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(TenantContext $tenant, Request $request)
    {
        $status = $request->get('status', 'active');

        $assignments = CampaignAssignment::whereIn(
            'campaign_id',
            $tenant->active()->campaigns()->pluck('id')
        )
            ->with(['creator', 'campaign', 'campaignProduct.product', 'order', 'submissions'])
            ->when($status !== 'all', function ($q) use ($status) {
                match ($status) {
                    'active' => $q->whereIn('status', ['accepted', 'contract_sent', 'order_created', 'shipped', 'delivered', 'in_progress']),
                    'submitted' => $q->whereIn('status', ['submitted', 'changes_requested']),
                    'completed' => $q->whereIn('status', ['approved', 'completed', 'cancelled']),
                    default => $q,
                };
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('brand.assignments.index', compact('assignments', 'status'));
    }

    public function show(CampaignAssignment $assignment, TenantContext $tenant)
    {
        abort_unless($assignment->campaign->workspace_id === $tenant->id(), 403);

        $eager = [
            'creator.nicheRows', 'creator.socialAccounts',
            'campaignProduct.product', 'order', 'submissions', 'contract', 'payout',
        ];
        if (\App\Support\SchemaCheck::has('campaign_references')) {
            $eager[] = 'campaign.references';
        }
        $assignment->load($eager);

        return view('brand.assignments.show', compact('assignment'));
    }

    public function approveContent(ContentSubmission $submission, Request $request, ApproveContent $approve, TenantContext $tenant)
    {
        abort_unless($submission->campaign->workspace_id === $tenant->id(), 403);

        $approve->handle($submission, $request->user()->id);

        return back()->with('status', 'Content approved and payout released.');
    }

    public function requestChanges(ContentSubmission $submission, Request $request, TenantContext $tenant)
    {
        abort_unless($submission->campaign->workspace_id === $tenant->id(), 403);

        $data = $request->validate(['comment' => ['required', 'string', 'max:2000']]);

        $submission->requestChanges($data['comment']);

        \App\Support\NotifyEvent::fire('creator.content.changes_requested', $submission->assignment->creator, [
            'creator_name'   => $submission->assignment->creator->display_name ?? '',
            'brand_name'     => $submission->campaign->workspace->name ?? '',
            'campaign_title' => $submission->campaign->title ?? '',
            'comment'        => $data['comment'],
            'link'           => route('creator.assignments.show', $submission->assignment),
        ]);

        return back()->with('status', 'Change request sent.');
    }
}

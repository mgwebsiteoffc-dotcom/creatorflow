<?php

namespace App\Http\Controllers\Creator;

use App\Domains\Campaigns\Actions\AcceptInvitation;
use App\Domains\Content\Actions\SubmitContent;
use App\Http\Controllers\Controller;
use App\Models\CampaignAssignment;
use App\Models\CampaignInvitation;
use App\Models\ContentSubmission;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function invitations(Request $request)
    {
        $invitations = CampaignInvitation::with(['campaign.workspace', 'campaignProduct.product'])
            ->where('creator_id', $request->user()->creator->id)
            ->where('status', 'sent')
            ->latest()
            ->paginate(20);

        return view('creator.invitations', compact('invitations'));
    }

    public function accept(CampaignInvitation $invitation, AcceptInvitation $accept, Request $request)
    {
        abort_unless($invitation->creator_id === $request->user()->creator->id, 403);

        if ($invitation->status !== 'sent') {
            return back()->with('error', 'This invitation is no longer active.');
        }

        $accept->handle($invitation);

        return redirect()->route('creator.assignments.show', $invitation->campaign->assignments()
            ->where('creator_id', $request->user()->creator->id)->first())
            ->with('status', 'You\'re in! Your agreement and order are being prepared.');
    }

    public function decline(CampaignInvitation $invitation, Request $request)
    {
        abort_unless($invitation->creator_id === $request->user()->creator->id, 403);

        $invitation->update(['status' => 'declined', 'responded_at' => now()]);

        return redirect()->route('creator.invitations')->with('status', 'Invitation declined.');
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'active');

        $assignments = CampaignAssignment::where('creator_id', $request->user()->creator->id)
            ->with(['campaign', 'campaignProduct.product', 'order', 'submissions'])
            ->when($status === 'active', fn ($q) => $q->whereIn('status', ['contract_sent', 'contract_signed', 'order_created', 'shipped', 'delivered', 'in_progress', 'submitted', 'changes_requested']))
            ->when($status === 'completed', fn ($q) => $q->whereIn('status', ['approved', 'completed', 'cancelled']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('creator.assignments.index', compact('assignments', 'status'));
    }

    public function show(CampaignAssignment $assignment, Request $request)
    {
        abort_unless($assignment->creator_id === $request->user()->creator->id, 403);

        $assignment->load([
            'campaign.workspace', 'campaignProduct.product.primaryImage',
            'campaignProduct.variant', 'order', 'contract', 'submissions', 'payout',
        ]);

        return view('creator.assignments.show', compact('assignment'));
    }

    public function signContract(CampaignAssignment $assignment, Request $request)
    {
        abort_unless($assignment->creator_id === $request->user()->creator->id, 403);

        $assignment->contract?->signAsCreator($request->ip());
        $assignment->update(['status' => 'contract_signed']);

        return back()->with('status', 'Agreement signed.');
    }

    public function submitContent(CampaignAssignment $assignment, Request $request, SubmitContent $submit)
    {
        abort_unless($assignment->creator_id === $request->user()->creator->id, 403);

        $data = $request->validate([
            'type' => ['required', 'in:image,video,story,reel,link'],
            'file' => ['required_without:external_post_url', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov', 'max:102400'],
            'caption' => ['nullable', 'string', 'max:5000'],
            'external_post_url' => ['nullable', 'url', 'max:500'],
        ]);

        if ($request->hasFile('file')) {
            $submission = $submit->handle(
                $assignment,
                $data['type'],
                $request->file('file'),
                ['caption' => $data['caption'] ?? null, 'external_post_url' => $data['external_post_url'] ?? null]
            );
        } else {
            $submission = ContentSubmission::create([
                'assignment_id' => $assignment->id,
                'creator_id' => $assignment->creator_id,
                'campaign_id' => $assignment->campaign_id,
                'type' => $data['type'],
                'disk' => 'local',
                'path' => 'external/'.$data['external_post_url'],
                'caption' => $data['caption'] ?? null,
                'external_post_url' => $data['external_post_url'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
            $assignment->update(['status' => 'submitted']);
        }

        return back()->with('status', 'Content submitted! It will be reviewed shortly.');
    }
}

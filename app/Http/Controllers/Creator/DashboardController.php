<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CampaignInvitation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $creator = $request->user()->creator;

        $invitations = CampaignInvitation::with(['campaign.workspace', 'campaignProduct.product'])
            ->where('creator_id', $creator->id)
            ->where('status', 'sent')
            ->latest()
            ->take(6)
            ->get();

        $activeAssignments = $creator->assignments()
            ->with(['campaign', 'campaignProduct.product', 'order'])
            ->whereIn('status', ['contract_sent', 'order_created', 'shipped', 'delivered', 'in_progress', 'submitted', 'changes_requested'])
            ->latest()
            ->take(8)
            ->get();

        $recentApplications = $creator->applications()
            ->with(['campaign.workspace'])
            ->latest()
            ->take(4)
            ->get();

        $applicationCounts = [
            'total'   => $creator->applications()->count(),
            'pending' => $creator->applications()->whereIn('status', ['submitted','shortlisted'])->count(),
        ];

        $earningsCents = (int) $creator->payouts()->where('status', 'paid')->sum('net_cents');
        $pendingCents = (int) $creator->payouts()->where('status', 'pending')->sum('amount_cents');

        return view('creator.dashboard', compact(
            'creator',
            'invitations',
            'activeAssignments',
            'recentApplications',
            'applicationCounts',
            'earningsCents',
            'pendingCents',
        ));
    }
}

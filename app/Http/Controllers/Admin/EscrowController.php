<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignAssignment;
use App\Models\EscrowTransaction;
use App\Models\Payout;
use Illuminate\Http\Request;

class EscrowController extends Controller
{
    public function index(Request $request)
    {
        $hasEscrow = \App\Support\SchemaCheck::has('escrow_transactions');

        if ($hasEscrow) {
            $transactions = EscrowTransaction::query()
                ->when($request->get('kind'), fn ($q, $k) => $q->where('kind', $k))
                ->with(['workspace:id,name', 'creator:id,display_name', 'assignment:id,campaign_id'])
                ->latest()
                ->paginate(30)
                ->withQueryString();

            $totals = [
                'held'     => (int) EscrowTransaction::where('kind', 'hold')->sum('amount_cents')
                              - (int) EscrowTransaction::whereIn('kind', ['release','refund'])->sum('amount_cents'),
                'released' => (int) EscrowTransaction::where('kind', 'release')->sum('amount_cents'),
                'refunded' => (int) EscrowTransaction::where('kind', 'refund')->sum('amount_cents'),
                'fees'     => (int) EscrowTransaction::where('kind', 'fee')->sum('amount_cents'),
            ];
        } else {
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 30);
            $totals = ['held' => 0, 'released' => 0, 'refunded' => 0, 'fees' => 0];
        }

        $pendingPayouts = Payout::with(['creator:id,display_name', 'assignment:id,campaign_id'])
            ->where('status', 'pending')
            ->latest()
            ->take(20)
            ->get();

        $schemaMissing = ! $hasEscrow;

        return view('admin.escrow.index', compact('transactions', 'totals', 'pendingPayouts', 'schemaMissing'));
    }

    public function hold(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => ['required', 'exists:campaign_assignments,id'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'note'          => ['nullable', 'string', 'max:500'],
        ]);
        $assignment = CampaignAssignment::findOrFail($data['assignment_id']);
        $amountCents = (int) round(((float) $data['amount']) * 100);

        EscrowTransaction::create([
            'workspace_id'  => $assignment->campaign->workspace_id,
            'creator_id'    => $assignment->creator_id,
            'assignment_id' => $assignment->id,
            'kind'          => 'hold',
            'amount_cents'  => $amountCents,
            'currency'      => $assignment->campaign->budget_currency,
            'note'          => $data['note'] ?? 'Manual hold',
            'performed_by'  => $request->user()->id,
        ]);

        return back()->with('status', "Held ₹".number_format($amountCents/100, 2, '.', ',')." in escrow.");
    }

    public function release(Payout $payout, Request $request)
    {
        $payout->markPaid();

        EscrowTransaction::create([
            'workspace_id'  => $payout->workspace_id,
            'creator_id'    => $payout->creator_id,
            'assignment_id' => $payout->assignment_id,
            'payout_id'     => $payout->id,
            'kind'          => 'release',
            'amount_cents'  => $payout->net_cents,
            'currency'      => $payout->currency,
            'note'          => 'Manual release by admin',
            'performed_by'  => $request->user()->id,
        ]);

        return back()->with('status', "Released ₹".number_format($payout->net_cents/100, 2, '.', ',')." to creator.");
    }

    public function refund(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => ['required', 'exists:campaign_assignments,id'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'note'          => ['nullable', 'string', 'max:500'],
        ]);
        $assignment = CampaignAssignment::findOrFail($data['assignment_id']);
        $amountCents = (int) round(((float) $data['amount']) * 100);

        EscrowTransaction::create([
            'workspace_id'  => $assignment->campaign->workspace_id,
            'creator_id'    => $assignment->creator_id,
            'assignment_id' => $assignment->id,
            'kind'          => 'refund',
            'amount_cents'  => $amountCents,
            'currency'      => $assignment->campaign->budget_currency,
            'note'          => $data['note'] ?? 'Refund to brand',
            'performed_by'  => $request->user()->id,
        ]);

        return back()->with('status', 'Refund recorded.');
    }
}

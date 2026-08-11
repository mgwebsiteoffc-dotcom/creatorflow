<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentRecord;
use App\Models\Payout;
use App\Models\Workspace;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $hasPayments = SchemaCheck::has('payment_records');
        $hasInvoices = SchemaCheck::has('invoices');

        $filter = $request->get('kind', 'all');

        // Payments (brand → platform + creator payouts)
        if ($hasPayments) {
            $payments = PaymentRecord::query()
                ->with(['workspace:id,name,currency', 'campaign:id,title'])
                ->when($filter !== 'all', fn ($q) => $q->where('kind', $filter))
                ->latest('paid_at')->latest()
                ->paginate(30)
                ->withQueryString();
            $totals = [
                'inflow'   => (int) PaymentRecord::where('direction', 'outflow')->where('status', 'succeeded')->sum('amount_cents'),
                'refunded' => (int) PaymentRecord::where('kind', 'refund')->sum('amount_cents'),
                'pending'  => (int) PaymentRecord::where('status', 'pending')->sum('amount_cents'),
            ];
        } else {
            $payments = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 30);
            $totals   = ['inflow' => 0, 'refunded' => 0, 'pending' => 0];
        }

        $invoices = $hasInvoices
            ? Invoice::query()
                ->with('workspace:id,name,currency')
                ->latest()->take(20)->get()
            : collect();

        $payouts = Payout::with(['creator:id,display_name', 'workspace:id,name,currency'])
            ->latest()->take(20)->get();

        $payoutTotals = [
            'paid'    => (int) Payout::where('status', 'paid')->sum('net_cents'),
            'pending' => (int) Payout::where('status', 'pending')->sum('net_cents'),
        ];

        // Revenue per workspace (top 10)
        $topWorkspaces = $hasPayments
            ? PaymentRecord::selectRaw('workspace_id, sum(amount_cents) as total')
                ->where('direction', 'outflow')->where('status', 'succeeded')
                ->groupBy('workspace_id')->orderByDesc('total')->take(10)->get()
                ->map(fn ($r) => (object) [
                    'workspace' => Workspace::find($r->workspace_id),
                    'total'     => (int) $r->total,
                ])
                ->filter(fn ($r) => $r->workspace)
            : collect();

        return view('admin.billing.index', compact(
            'payments', 'invoices', 'payouts', 'totals', 'payoutTotals', 'filter',
            'topWorkspaces', 'hasPayments', 'hasInvoices'
        ));
    }
}

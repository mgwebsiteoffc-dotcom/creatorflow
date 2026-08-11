<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(TenantContext $tenant, Request $request)
    {
        $workspace = $tenant->active()->load(['subscription']);

        $filter = $request->get('kind', 'all');

        $payments = $workspace->paymentRecords()
            ->with(['campaign:id,title'])
            ->when($filter !== 'all', fn ($q) => $q->where('kind', $filter))
            ->latest('paid_at')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $invoices = $workspace->invoices()->latest()->take(10)->get();

        $totals = [
            'paid_this_month' => (int) $workspace->paymentRecords()
                ->where('direction', 'outflow')->where('status', 'succeeded')
                ->whereMonth('created_at', now()->month)
                ->sum('amount_cents'),
            'paid_lifetime'   => (int) $workspace->paymentRecords()
                ->where('direction', 'outflow')->where('status', 'succeeded')
                ->sum('amount_cents'),
            'refunded'        => (int) $workspace->paymentRecords()
                ->where('kind', 'refund')->sum('amount_cents'),
            'pending'         => (int) $workspace->paymentRecords()
                ->where('status', 'pending')->sum('amount_cents'),
        ];

        return view('brand.billing.index', compact('workspace', 'payments', 'invoices', 'totals', 'filter'));
    }

    public function storePayment(Request $request, TenantContext $tenant)
    {
        $data = $request->validate([
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'description' => ['nullable', 'string', 'max:190'],
            'reference'   => ['nullable', 'string', 'max:190'],
            'kind'        => ['required', 'in:subscription,campaign,top_up,refund,adjustment'],
            'amount_cents'=> ['required', 'integer', 'min:1'],
            'method'      => ['nullable', 'in:card,upi,bank,manual'],
            'paid_at'     => ['nullable', 'date'],
            'receipt_url' => ['nullable', 'url', 'max:500'],
        ]);

        $workspace = $tenant->active();

        PaymentRecord::create($data + [
            'workspace_id' => $workspace->id,
            'currency'     => $workspace->currency,
            'direction'    => $data['kind'] === 'refund' ? 'inflow' : 'outflow',
            'status'       => 'succeeded',
            'paid_at'      => $data['paid_at'] ?? now(),
            'recorded_by'  => $request->user()->id,
        ]);

        return back()->with('status', 'Payment recorded.');
    }
}

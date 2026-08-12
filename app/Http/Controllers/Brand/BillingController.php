<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class BillingController extends Controller
{
    public function index(TenantContext $tenant, Request $request)
    {
        $workspace = $tenant->active()->load(['subscription']);
        $filter    = $request->get('kind', 'all');

        // Only touch payment_records if the migration has actually run.
        $hasPayments  = Schema::hasTable('payment_records');
        $hasInvoices  = Schema::hasTable('invoices');

        if ($hasPayments) {
            $payments = $workspace->paymentRecords()
                ->with(['campaign:id,title'])
                ->when($filter !== 'all', fn ($q) => $q->where('kind', $filter))
                ->latest('paid_at')
                ->latest()
                ->paginate(30)
                ->withQueryString();

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
        } else {
            // Graceful empty state — the page renders fine, with an admin banner
            // asking someone to run `php artisan migrate`.
            $payments = new LengthAwarePaginator(new Collection(), 0, 30);
            $totals   = ['paid_this_month' => 0, 'paid_lifetime' => 0, 'refunded' => 0, 'pending' => 0];
        }

        $invoices = $hasInvoices
            ? $workspace->invoices()->latest()->take(10)->get()
            : new Collection();

        $schemaMissing = ! $hasPayments;

        return view('brand.billing.index',
            compact('workspace', 'payments', 'invoices', 'totals', 'filter', 'schemaMissing'));
    }

    public function storePayment(Request $request, TenantContext $tenant)
    {
        if (! Schema::hasTable('payment_records')) {
            return back()->with('error', 'Billing schema not yet migrated. Please run `php artisan migrate`.');
        }

        $data = $request->validate([
            'campaign_id'  => ['nullable', 'exists:campaigns,id'],
            'description'  => ['nullable', 'string', 'max:190'],
            'reference'    => ['nullable', 'string', 'max:190'],
            'kind'         => ['required', 'in:subscription,campaign,top_up,refund,adjustment'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'method'       => ['nullable', 'in:card,upi,bank,manual'],
            'paid_at'      => ['nullable', 'date'],
            'receipt_url'  => ['nullable', 'url', 'max:500'],
        ]);

        $workspace = $tenant->active();

        $amountCents = (int) round(((float) $data['amount']) * 100);
        unset($data['amount']);

        PaymentRecord::create($data + [
            'workspace_id' => $workspace->id,
            'amount_cents' => $amountCents,
            'currency'     => $workspace->currency,
            'direction'    => $data['kind'] === 'refund' ? 'inflow' : 'outflow',
            'status'       => 'succeeded',
            'paid_at'      => $data['paid_at'] ?? now(),
            'recorded_by'  => $request->user()->id,
        ]);

        return back()->with('status', 'Payment recorded.');
    }
}

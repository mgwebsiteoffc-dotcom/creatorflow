<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\EscrowTransaction;
use App\Models\EventLog;
use App\Models\PaymentRecord;
use App\Support\RazorpayService;
use App\Support\SchemaCheck;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Escrow top-up flow for brands.
 *
 * Flow:
 *   GET  /brand/billing/top-up            → form (pick amount)
 *   POST /brand/billing/top-up            → creates a Razorpay order, returns
 *                                            the checkout keys to the client
 *   POST /brand/billing/top-up/confirm    → server-side verify signature +
 *                                            write PaymentRecord + EscrowTransaction
 *   POST /brand/billing/top-up/manual     → admin-style path when Razorpay
 *                                            isn't configured (records the
 *                                            top-up as 'succeeded' manual so
 *                                            the balance moves — same shape).
 */
class EscrowTopUpController extends Controller
{
    public function show(TenantContext $tenant, RazorpayService $razorpay)
    {
        $workspace = $tenant->active();

        // Current balance = SUM(holds) − SUM(release + refund) for this workspace.
        $balanceCents = 0;
        $recentTx = collect();
        if (SchemaCheck::has('escrow_transactions')) {
            $held    = (int) EscrowTransaction::where('workspace_id', $workspace->id)
                        ->where('kind', 'hold')->sum('amount_cents');
            $out     = (int) EscrowTransaction::where('workspace_id', $workspace->id)
                        ->whereIn('kind', ['release', 'refund'])->sum('amount_cents');
            $balanceCents = $held - $out;

            $recentTx = EscrowTransaction::where('workspace_id', $workspace->id)
                ->latest()->take(10)->get();
        }

        return view('brand.billing.top-up', [
            'workspace'      => $workspace,
            'balanceCents'   => $balanceCents,
            'recentTx'       => $recentTx,
            'razorpayReady'  => $razorpay->isConfigured(),
            'razorpayKey'    => $razorpay->isConfigured() ? $razorpay->publicKey() : null,
            'razorpayIsLive' => $razorpay->isConfigured() ? $razorpay->isLive() : false,
        ]);
    }

    /** Create the Razorpay order and return the config the JS checkout needs. */
    public function create(Request $request, TenantContext $tenant, RazorpayService $razorpay)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:1000000'], // ₹100 – ₹10L
        ]);

        if (! $razorpay->isConfigured()) {
            return response()->json([
                'error' => 'Razorpay isn\'t configured yet. Ask an admin to add keys under /admin/integrations.',
            ], 422);
        }

        $workspace   = $tenant->active();
        $amountCents = (int) round(((float) $data['amount']) * 100);

        try {
            $order = $razorpay->createOrder($amountCents, $workspace->currency ?: 'INR', [
                'workspace_id' => (string) $workspace->id,
                'purpose'      => 'escrow_topup',
                'user_id'      => (string) $request->user()->id,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Could not start payment. Try again in a moment.'], 502);
        }

        return response()->json([
            'ok'            => true,
            'order_id'      => $order['id'],
            'amount_cents'  => $amountCents,
            'currency'      => $workspace->currency ?: 'INR',
            'razorpay_key'  => $razorpay->publicKey(),
            'workspace'     => $workspace->name,
            'brand_name'    => 'CreatorPlex',
            'prefill'       => [
                'email'   => $request->user()->email,
                'name'    => $request->user()->name,
                'contact' => $request->user()->phone ?? '',
            ],
            'is_live'       => $razorpay->isLive(),
        ]);
    }

    /** Called after the JS checkout resolves. Verifies signature and books
     *  a PaymentRecord + EscrowTransaction inside a single transaction. */
    public function confirm(Request $request, TenantContext $tenant, RazorpayService $razorpay)
    {
        $data = $request->validate([
            'razorpay_order_id'   => ['required', 'string', 'max:190'],
            'razorpay_payment_id' => ['required', 'string', 'max:190'],
            'razorpay_signature'  => ['required', 'string', 'max:190'],
            'amount_cents'        => ['required', 'integer', 'min:1'],
        ]);

        if (! $razorpay->verifyPaymentSignature($data['razorpay_order_id'], $data['razorpay_payment_id'], $data['razorpay_signature'])) {
            return response()->json(['error' => 'Payment signature verification failed.'], 400);
        }

        $workspace = $tenant->active();
        $userId    = $request->user()->id;

        DB::transaction(function () use ($data, $workspace, $userId) {
            PaymentRecord::create([
                'workspace_id' => $workspace->id,
                'kind'         => 'top_up',
                'direction'    => 'outflow',
                'status'       => 'succeeded',
                'method'       => 'card',
                'amount_cents' => (int) $data['amount_cents'],
                'currency'     => $workspace->currency ?: 'INR',
                'description'  => 'Escrow top-up',
                'reference'    => $data['razorpay_payment_id'],
                'paid_at'      => now(),
                'recorded_by'  => $userId,
            ]);

            EscrowTransaction::create([
                'workspace_id' => $workspace->id,
                'kind'         => 'hold',
                'amount_cents' => (int) $data['amount_cents'],
                'currency'     => $workspace->currency ?: 'INR',
                'reference'    => $data['razorpay_payment_id'],
                'note'         => 'Brand top-up via Razorpay',
                'performed_by' => $userId,
            ]);

            EventLog::record('escrow.topup', 'Workspace', $workspace->id, [
                'amount_cents' => (int) $data['amount_cents'],
                'razorpay_id'  => $data['razorpay_payment_id'],
            ]);
        });

        return response()->json([
            'ok'      => true,
            'message' => '₹'.number_format($data['amount_cents'] / 100, 2, '.', ',').' added to your escrow.',
            'next'    => route('brand.escrow.top-up'),
        ]);
    }

    /**
     * Manual top-up path when Razorpay is not configured (dev / demo).
     * Records the movement so the flow can be end-to-end tested.
     */
    public function manual(Request $request, TenantContext $tenant)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:1000000'],
            'note'   => ['nullable', 'string', 'max:190'],
        ]);

        $workspace   = $tenant->active();
        $amountCents = (int) round(((float) $data['amount']) * 100);
        $userId      = $request->user()->id;

        DB::transaction(function () use ($amountCents, $workspace, $userId, $data) {
            PaymentRecord::create([
                'workspace_id' => $workspace->id,
                'kind'         => 'top_up',
                'direction'    => 'outflow',
                'status'       => 'succeeded',
                'method'       => 'manual',
                'amount_cents' => $amountCents,
                'currency'     => $workspace->currency ?: 'INR',
                'description'  => 'Escrow top-up (manual)',
                'paid_at'      => now(),
                'recorded_by'  => $userId,
            ]);

            EscrowTransaction::create([
                'workspace_id' => $workspace->id,
                'kind'         => 'hold',
                'amount_cents' => $amountCents,
                'currency'     => $workspace->currency ?: 'INR',
                'note'         => $data['note'] ?? 'Manual top-up',
                'performed_by' => $userId,
            ]);
        });

        return redirect()->route('brand.escrow.top-up')
            ->with('status', '₹'.number_format($amountCents / 100, 2, '.', ',').' added to your escrow.');
    }
}

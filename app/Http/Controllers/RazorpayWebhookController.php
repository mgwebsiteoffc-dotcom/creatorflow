<?php

namespace App\Http\Controllers;

use App\Models\EventLog;
use App\Models\PaymentRecord;
use App\Models\Workspace;
use App\Support\RazorpayService;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

class RazorpayWebhookController extends Controller
{
    public function __invoke(Request $request, RazorpayService $razorpay)
    {
        $payload   = $request->getContent();
        $signature = (string) $request->header('X-Razorpay-Signature', '');

        if (! $razorpay->verifyWebhook($payload, $signature)) {
            return response('invalid signature', 401);
        }

        $data = $request->json()->all();
        $event = $data['event'] ?? 'unknown';
        $payment = data_get($data, 'payload.payment.entity', []);
        $order   = data_get($data, 'payload.order.entity', []);

        // Reconcile a payment record — best-effort. We look up the workspace by
        // the notes we passed at order creation time.
        if (SchemaCheck::has('payment_records') && ! empty($payment['id'])) {
            $workspaceId = (int) (data_get($order, 'notes.workspace_id') ?: data_get($payment, 'notes.workspace_id') ?: 0);

            PaymentRecord::updateOrCreate(
                ['external_id' => (string) $payment['id']],
                [
                    'workspace_id' => $workspaceId ?: null,
                    'kind'         => 'subscription',
                    'direction'    => 'inflow',
                    'method'       => 'razorpay',
                    'amount_cents' => (int) ($payment['amount'] ?? 0),
                    'currency'     => $payment['currency'] ?? 'INR',
                    'status'       => match ($payment['status'] ?? '') {
                        'captured', 'authorized' => 'succeeded',
                        'refunded'                => 'refunded',
                        'failed'                  => 'failed',
                        default                   => 'pending',
                    },
                    'description'  => "Razorpay {$event}",
                    'paid_at'      => ! empty($payment['created_at']) ? now()->setTimestamp((int) $payment['created_at']) : now(),
                ]
            );
        }

        EventLog::record('razorpay.webhook.'.$event, 'Workspace', $workspaceId ?? 0, [
            'payment_id' => $payment['id'] ?? null,
            'amount'     => $payment['amount'] ?? null,
        ]);

        // ─── RazorpayX payout status updates ───
        $payoutEntity = data_get($data, 'payload.payout.entity', []);
        if (str_starts_with($event, 'payout.') && ! empty($payoutEntity['id'])) {
            $localPayout = \App\Models\Payout::where('external_id', $payoutEntity['id'])->first();
            if ($localPayout) {
                $localPayout->update([
                    'external_status' => $payoutEntity['status'] ?? null,
                    'status'          => match ($payoutEntity['status'] ?? '') {
                        'processed'         => 'paid',
                        'processing','queued','pending' => 'pending',
                        'reversed','rejected','failed','cancelled' => 'failed',
                        default             => $localPayout->status,
                    },
                    'failure_reason'  => $payoutEntity['failure_reason'] ?? null,
                    'paid_at'         => ($payoutEntity['status'] ?? '') === 'processed' ? now() : $localPayout->paid_at,
                ]);
            }
        }

        // Send receipt email + WhatsApp to the brand on successful capture.
        if (in_array($event, ['payment.captured', 'subscription.charged'], true) && ! empty($workspaceId)) {
            $ws = \App\Models\Workspace::find($workspaceId);
            if ($ws) {
                \App\Support\NotifyEvent::fire('brand.payment.received', $ws, [
                    'brand_name' => $ws->name,
                    'amount'     => number_format(((int) ($payment['amount'] ?? 0)) / 100, 2, '.', ','),
                    'link'       => url('/brand/billing'),
                ]);
            }
        }

        return response('ok', 200);
    }
}

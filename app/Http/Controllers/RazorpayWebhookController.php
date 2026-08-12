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

        return response('ok', 200);
    }
}

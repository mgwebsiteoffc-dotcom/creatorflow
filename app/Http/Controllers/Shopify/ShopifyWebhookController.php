<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Jobs\HandleShopifyWebhook;
use App\Models\Channel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopifyWebhookController extends Controller
{
    /**
     * Entry point for all Shopify webhooks. Verifies the HMAC signature,
     * resolves the workspace from the shop domain, and dispatches a queued
     * job so we can return 200 within Shopify's timeout.
     */
    public function __invoke(Request $request): Response
    {
        $topic = $request->header('x-shopify-topic');
        $shop = $request->header('x-shopify-shop-domain');
        $hmac = $request->header('x-shopify-hmac-sha256');
        $webhookId = $request->header('x-shopify-webhook-id');

        if (! $topic || ! $shop) {
            return response('Missing headers', 400);
        }

        if (! $this->verifyHmac($request, $hmac)) {
            return response('Invalid HMAC', 401);
        }

        $channel = Channel::where('type', 'shopify')
            ->where('external_id', $shop)
            ->first();

        if (! $channel) {
            return response('Unknown shop', 404);
        }

        HandleShopifyWebhook::dispatch(
            $channel->workspace_id,
            $topic,
            $request->all(),
            $webhookId
        );

        return response('OK', 200);
    }

    protected function verifyHmac(Request $request, ?string $hmac): bool
    {
        $secret = config('creatorflow.shopify.webhook_secret');

        // Allow unsigned local/test webhooks when no secret is configured.
        if (empty($secret) || config('creatorflow.demo.fake_external_calls')) {
            return true;
        }

        if (empty($hmac)) {
            return false;
        }

        $calculated = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));

        return hash_equals($calculated, $hmac);
    }
}

<?php

namespace App\Jobs;

use App\Domains\Analytics\Actions\AttributeOrder;
use App\Domains\Commerce\Channels\Shopify\ShopifyProductTransformer;
use App\Models\Channel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class HandleShopifyWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $workspaceId,
        public string $topic,
        public array $payload,
        public ?string $webhookId = null,
    ) {}

    public function handle(ShopifyProductTransformer $transformer, AttributeOrder $attributeOrder): void
    {
        $workspace = Workspace::find($this->workspaceId);
        if (! $workspace) {
            return;
        }

        $channel = $workspace->channels()->where('type', 'shopify')->first();
        if (! $channel instanceof Channel) {
            return;
        }

        match ($this->topic) {
            'products/create', 'products/update' => $this->handleProductUpsert($workspace, $channel, $transformer),
            'products/delete' => $this->handleProductDelete($workspace),
            'inventory_levels/update' => $this->handleInventoryUpdate($workspace),
            'orders/create', 'orders/updated' => $this->handleOrderUpsert($workspace, $attributeOrder),
            'app/uninstalled' => $this->handleUninstall($channel),
            'customers/data_request', 'customers/redact', 'shop/redact' => $this->handleGdpr($workspace),
            default => null,
        };
    }

    protected function handleProductUpsert(Workspace $workspace, Channel $channel, ShopifyProductTransformer $transformer): void
    {
        $transformer->upsert($workspace->id, $channel->id, $this->payload);
    }

    protected function handleProductDelete(Workspace $workspace): void
    {
        $externalId = (string) ($this->payload['id'] ?? '');
        if ($externalId === '') {
            return;
        }

        $workspace->products()->where('external_id', $externalId)->delete();
    }

    protected function handleInventoryUpdate(Workspace $workspace): void
    {
        // A full reconcile is queued; granular variant updates would map the
        // inventory_item_id to a product variant and adjust inventory_qty.
        $channel = $workspace->channels()->where('type', 'shopify')->first();
        if ($channel) {
            SyncChannel::dispatch($workspace->id, $channel->id, 'inventory');
        }
    }

    protected function handleOrderUpsert(Workspace $workspace, AttributeOrder $attributeOrder): void
    {
        $externalId = (string) ($this->payload['id'] ?? '');

        $order = Order::updateOrCreate(
            ['workspace_id' => $workspace->id, 'external_id' => $externalId],
            [
                'channel_id' => $workspace->channels()->where('type', 'shopify')->value('id'),
                'order_number' => '#'.($this->payload['order_number'] ?? $externalId),
                'email' => $this->payload['email'] ?? null,
                'subtotal_cents' => $this->toCents($this->payload['subtotal_price'] ?? 0),
                'total_discount_cents' => $this->toCents($this->payload['total_discounts'] ?? 0),
                'total_cents' => $this->toCents($this->payload['total_price'] ?? 0),
                'currency' => $this->payload['currency'] ?? $workspace->currency,
                'status' => $this->mapOrderStatus($this->payload),
                'shipping_address' => $this->payload['shipping_address'] ?? null,
                'tracking_number' => $this->payload['fulfillments'][0]['tracking_number'] ?? null,
                'tracking_company' => $this->payload['fulfillments'][0]['tracking_company'] ?? null,
                'placed_at' => isset($this->payload['created_at']) ? Carbon\Carbon::parse($this->payload['created_at']) : now(),
                'fulfilled_at' => isset($this->payload['closed_at']) ? Carbon\Carbon::parse($this->payload['closed_at']) : null,
                'raw_payload' => $this->payload,
            ]
        );

        $this->syncOrderItems($order);
        $attributeOrder->handle($order);
    }

    protected function syncOrderItems(Order $order): void
    {
        $order->items()->delete();

        foreach (($this->payload['line_items'] ?? []) as $item) {
            $product = $order->workspace->products()
                ->where('external_id', (string) ($item['product_id'] ?? ''))
                ->first();

            $order->items()->create([
                'product_id' => $product?->id,
                'title' => $item['title'] ?? 'Item',
                'sku' => $item['sku'] ?? null,
                'quantity' => (int) ($item['quantity'] ?? 1),
                'price_cents' => $this->toCents($item['price'] ?? 0),
                'total_discount_cents' => $this->toCents($item['total_discount'] ?? 0),
            ]);
        }
    }

    protected function handleUninstall(Channel $channel): void
    {
        $channel->update(['status' => 'disconnected', 'credentials' => null]);
        $channel->workspace->update(['plan_status' => 'inactive']);
    }

    protected function handleGdpr(Workspace $workspace): void
    {
        // Queue a configurable data purge/export job per Shopify's GDPR rules.
        // For now, record the request in the audit log.
        \App\Models\AuditLog::create([
            'workspace_id' => $workspace->id,
            'actor_type' => 'system',
            'action' => 'gdpr.'.$this->topic,
            'subject_type' => 'workspace',
            'subject_id' => $workspace->id,
            'changes' => ['payload' => $this->payload],
            'created_at' => now(),
        ]);
    }

    protected function mapOrderStatus(array $payload): string
    {
        if (($payload['cancelled_at'] ?? null) !== null) {
            return 'cancelled';
        }
        if (($payload['financial_status'] ?? null) === 'refunded') {
            return 'refunded';
        }
        if (($payload['fulfillment_status'] ?? null) === 'fulfilled') {
            return 'fulfilled';
        }

        return 'paid';
    }

    protected function toCents(mixed $value): int
    {
        return (int) round(((float) $value) * 100);
    }
}

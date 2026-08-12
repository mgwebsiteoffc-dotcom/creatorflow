<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Jobs\CreateCreatorOrder;
use App\Models\CampaignAssignment;
use App\Models\Channel;
use App\Models\Creator;
use App\Models\EventLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Barter / seeding order fulfillment for brands.
 *
 * Once a creator accepts, an assignment is created. If Shopify is connected,
 * a draft order is auto-created (see CreateCreatorOrder). If not, the brand
 * uses these screens to manually record shipping details.
 */
class OrderController extends Controller
{
    public function index(Request $request, TenantContext $tenant)
    {
        $workspace = $tenant->active();
        $status = $request->get('status', 'all');

        $orders = Order::where('workspace_id', $workspace->id)
            ->with(['creator', 'items.product', 'items.variant', 'channel'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest('placed_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Assignments that don't have an order yet — waiting to be shipped
        $needsFulfillment = CampaignAssignment::whereIn('campaign_id', $workspace->campaigns()->pluck('id'))
            ->whereIn('status', ['accepted', 'contract_signed'])
            ->whereNull('channel_order_id')
            ->with(['creator.preferences', 'campaign', 'campaignProduct.product', 'campaignProduct.variant'])
            ->latest()->take(10)->get();

        $stats = [
            'total'      => Order::where('workspace_id', $workspace->id)->count(),
            'pending'    => Order::where('workspace_id', $workspace->id)->whereIn('status', ['draft','open','paid'])->count(),
            'fulfilled'  => Order::where('workspace_id', $workspace->id)->whereIn('status', ['fulfilled','delivered'])->count(),
            'needs_ship' => $needsFulfillment->count(),
        ];

        // Detect whether a Shopify (or any non-manual) channel is connected.
        // When only the manual channel exists we show the "Create manual order"
        // CTA and hide any Shopify-specific banners.
        $shopifyConnected = $workspace->channels()
            ->where('type', 'shopify')
            ->where('status', 'active')
            ->exists();
        $hasAnyRemoteChannel = $workspace->channels()
            ->whereNotIn('type', ['manual'])
            ->where('status', 'active')
            ->exists();

        return view('brand.orders.index', compact(
            'orders', 'needsFulfillment', 'stats', 'status',
            'shopifyConnected', 'hasAnyRemoteChannel'
        ));
    }

    /**
     * Manual "create a gifting order" form — used when the brand has no
     * Shopify connected and just wants to ship a product to a creator.
     */
    public function createManual(TenantContext $tenant)
    {
        $workspace = $tenant->active();

        $creators = Creator::query()
            ->where('status', 'active')
            ->orderBy('display_name')
            ->limit(500)
            ->get(['id', 'uuid', 'display_name', 'city', 'email']);

        $products = Product::where('workspace_id', $workspace->id)
            ->where('status', 'active')
            ->with(['variants:id,product_id,title,sku,price_cents,inventory_qty'])
            ->orderBy('title')
            ->limit(500)
            ->get(['id', 'uuid', 'title']);

        return view('brand.orders.create', compact('workspace', 'creators', 'products'));
    }

    public function storeManual(Request $request, TenantContext $tenant)
    {
        $workspace = $tenant->active();

        $data = $request->validate([
            'creator_id'         => ['required', 'exists:creators,id'],
            'product_id'         => ['required', 'exists:products,id'],
            'variant_id'         => ['nullable', 'exists:product_variants,id'],
            'quantity'           => ['required', 'integer', 'min:1', 'max:99'],
            'note'               => ['nullable', 'string', 'max:500'],
            'ship_address_line1' => ['required', 'string', 'max:190'],
            'ship_address_line2' => ['nullable', 'string', 'max:190'],
            'ship_city'          => ['required', 'string', 'max:120'],
            'ship_state'         => ['nullable', 'string', 'max:120'],
            'ship_postal'        => ['required', 'string', 'max:20'],
            'ship_country'       => ['nullable', 'string', 'max:80'],
            'ship_phone'         => ['nullable', 'string', 'max:40'],
        ]);

        $product = Product::where('workspace_id', $workspace->id)->findOrFail($data['product_id']);
        $variant = $product->variants()->find($data['variant_id'] ?? null) ?? $product->variants()->first();
        $price   = (int) ($variant?->price_cents ?? 0);

        // Ensure a manual channel exists so the order has somewhere to live.
        $channel = Channel::firstOrCreate(
            ['workspace_id' => $workspace->id, 'type' => 'manual'],
            ['name' => 'Manual', 'status' => 'active']
        );

        $address = [
            'line1'   => $data['ship_address_line1'],
            'line2'   => $data['ship_address_line2'] ?? null,
            'city'    => $data['ship_city'],
            'state'   => $data['ship_state'] ?? null,
            'postal'  => $data['ship_postal'],
            'country' => $data['ship_country'] ?? 'IN',
            'phone'   => $data['ship_phone'] ?? null,
        ];

        $orderNumber = 'MAN-' . strtoupper(Str::random(6));

        $order = Order::create([
            'workspace_id'         => $workspace->id,
            'channel_id'           => $channel->id,
            'creator_id'           => $data['creator_id'],
            'external_id'          => 'manual_' . Str::uuid()->toString(),
            'order_number'         => $orderNumber,
            'subtotal_cents'       => $price * $data['quantity'],
            'total_discount_cents' => $price * $data['quantity'],
            'total_cents'          => 0,
            'currency'             => $workspace->currency ?: 'INR',
            'status'               => 'open',
            'shipping_address'     => $address,
            'placed_at'            => now(),
            'raw_payload'          => [
                'manual'     => true,
                'note'       => $data['note'] ?? null,
                'created_by' => $request->user()->id,
            ],
        ]);

        OrderItem::create([
            'order_id'             => $order->id,
            'product_id'           => $product->id,
            'variant_id'           => $variant?->id,
            'title'                => $product->title,
            'sku'                  => $variant?->sku,
            'quantity'             => $data['quantity'],
            'price_cents'          => $price,
            'total_discount_cents' => $price * $data['quantity'],
        ]);

        if ($variant && $variant->inventory_qty > 0) {
            $variant->decrement('inventory_qty', min($data['quantity'], $variant->inventory_qty));
        }

        EventLog::record('order.manual_created', 'Order', $order->id, [
            'creator_id' => $data['creator_id'],
            'product_id' => $product->id,
        ]);

        return redirect()->route('brand.orders.show', $order)
            ->with('status', "Manual order {$orderNumber} created. Add tracking below when you ship.");
    }

    public function show(Order $order, TenantContext $tenant)
    {
        abort_unless($order->workspace_id === $tenant->id(), 403);

        $order->load(['creator.preferences', 'items.product.primaryImage', 'items.variant', 'channel']);
        $assignment = CampaignAssignment::where('channel_order_id', $order->id)
            ->with('campaign', 'campaignProduct')
            ->first();

        return view('brand.orders.show', compact('order', 'assignment'));
    }

    /**
     * For barter/seeding: create the ship-to order for a still-pending assignment.
     * Uses CreateCreatorOrder job which dispatches to the workspace's active
     * commerce channel (Shopify draft order, or manual local order).
     */
    public function createFromAssignment(CampaignAssignment $assignment, TenantContext $tenant)
    {
        abort_unless($assignment->campaign->workspace_id === $tenant->id(), 403);

        if ($assignment->channel_order_id) {
            return back()->with('status', 'Order already created for this creator.');
        }

        // Fire synchronously so the brand sees the new order immediately.
        CreateCreatorOrder::dispatchSync($assignment->id);

        return back()->with('status', "Order created for {$assignment->creator->display_name}. Ship the product and enter tracking below.");
    }

    /**
     * Record shipping details manually. Also pushes the tracking to Shopify
     * (via fulfillment endpoint) when the order came from a Shopify channel.
     */
    public function updateShipping(Order $order, Request $request, TenantContext $tenant)
    {
        abort_unless($order->workspace_id === $tenant->id(), 403);

        $data = $request->validate([
            'tracking_number'  => ['nullable', 'string', 'max:190'],
            'tracking_company' => ['nullable', 'string', 'max:190'],
            'status'           => ['nullable', 'in:draft,open,paid,fulfilled,cancelled,refunded,returned'],
            'ship_address_line1' => ['nullable', 'string', 'max:190'],
            'ship_address_line2' => ['nullable', 'string', 'max:190'],
            'ship_city'          => ['nullable', 'string', 'max:120'],
            'ship_state'         => ['nullable', 'string', 'max:120'],
            'ship_postal'        => ['nullable', 'string', 'max:20'],
            'ship_country'       => ['nullable', 'string', 'max:80'],
            'ship_phone'         => ['nullable', 'string', 'max:40'],
        ]);

        $address = array_filter([
            'line1'   => $data['ship_address_line1'] ?? null,
            'line2'   => $data['ship_address_line2'] ?? null,
            'city'    => $data['ship_city'] ?? null,
            'state'   => $data['ship_state'] ?? null,
            'postal'  => $data['ship_postal'] ?? null,
            'country' => $data['ship_country'] ?? null,
            'phone'   => $data['ship_phone'] ?? null,
        ]);

        $wasShipped = in_array($order->status, ['fulfilled', 'delivered']);

        $order->update([
            'tracking_number'  => $data['tracking_number'] ?? $order->tracking_number,
            'tracking_company' => $data['tracking_company'] ?? $order->tracking_company,
            'status'           => $data['status'] ?? $order->status,
            'shipping_address' => $address ?: $order->shipping_address,
            'fulfilled_at'     => ($data['status'] ?? null) === 'fulfilled' && ! $wasShipped ? now() : $order->fulfilled_at,
            'delivered_at'     => ($data['status'] ?? null) === 'delivered' ? now() : $order->delivered_at,
        ]);

        // Bump the linked assignment status so the creator sees "Shipped" / "Delivered".
        if ($assignment = CampaignAssignment::where('channel_order_id', $order->id)->first()) {
            $assignment->update([
                'status' => match ($order->status) {
                    'fulfilled' => 'shipped',
                    'delivered' => 'delivered',
                    default     => $assignment->status,
                },
            ]);
        }

        EventLog::record('order.shipping_updated', 'Order', $order->id, [
            'tracking' => $data['tracking_number'] ?? null,
            'status'   => $order->status,
        ]);

        // Fire creator email + WhatsApp only when tracking transitions to shipped/delivered.
        if (in_array($order->status, ['fulfilled', 'delivered'], true) && ! empty($data['tracking_number'])) {
            \App\Support\NotifyEvent::fire('creator.order.shipped', $order->creator, [
                'creator_name'     => $order->creator->display_name ?? '',
                'brand_name'       => $order->workspace->name ?? '',
                'product_title'    => optional($order->items->first())->title ?: 'your product',
                'tracking_number'  => $order->tracking_number,
                'tracking_company' => $order->tracking_company ?: '—',
                'link'             => url('/creator/assignments'),
            ]);
        }

        // Best-effort: push tracking to Shopify if this order originated there.
        if ($order->channel && $order->channel->type === 'shopify' && ! empty($order->external_id) && ! empty($data['tracking_number'])) {
            try {
                $apiVersion = data_get($order->channel->workspace->settings, 'api_version', '2025-01');
                app(\App\Domains\Commerce\Channels\ChannelRegistry::class)
                    ->forModel($order->channel)
                    ->client($order->workspace)
                    ->post("/admin/api/{$apiVersion}/orders/{$order->external_id}/fulfillments.json", [
                        'fulfillment' => [
                            'tracking_number'  => $data['tracking_number'],
                            'tracking_company' => $data['tracking_company'] ?? null,
                            'notify_customer'  => true,
                        ],
                    ]);
            } catch (\Throwable $e) {
                // Non-blocking: local record is still updated.
                report($e);
            }
        }

        return back()->with('status', 'Shipping details saved.');
    }

    /**
     * Cancel an order (e.g. creator ghosted, item out of stock).
     */
    public function cancel(Order $order, Request $request, TenantContext $tenant)
    {
        abort_unless($order->workspace_id === $tenant->id(), 403);

        $reason = $request->input('reason');
        $order->update(['status' => 'cancelled']);

        if ($assignment = CampaignAssignment::where('channel_order_id', $order->id)->first()) {
            $assignment->update(['status' => 'cancelled']);
        }

        EventLog::record('order.cancelled', 'Order', $order->id, ['reason' => $reason]);

        return back()->with('status', 'Order cancelled.');
    }
}

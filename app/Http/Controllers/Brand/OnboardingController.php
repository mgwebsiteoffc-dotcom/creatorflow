<?php

namespace App\Http\Controllers\Brand;

use App\Domains\AI\Actions\AnalyzeStore;
use App\Http\Controllers\Controller;
use App\Jobs\SyncChannel;
use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function show(TenantContext $tenant)
    {
        $workspace = $tenant->active();

        return view('brand.onboarding', [
            'workspace' => $workspace,
            'productsCount' => $workspace->products()->count(),
            'shopifyConnected' => $workspace->channels()->where('type', 'shopify')->exists(),
        ]);
    }

    public function connectShopify()
    {
        return redirect()->route('shopify.install');
    }

    public function storeManualProduct(Request $request, TenantContext $tenant, AnalyzeStore $analyze)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'product_type' => ['nullable', 'string', 'max:190'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'inventory_qty' => ['required', 'integer', 'min:0'],
        ]);

        $workspace = $tenant->active();
        $channel = $workspace->channels()->firstOrCreate(
            ['type' => 'manual'],
            ['name' => 'Manual', 'status' => 'active']
        );

        $product = Product::create([
            'uuid' => (string) Str::uuid(),
            'workspace_id' => $workspace->id,
            'channel_id' => $channel->id,
            'external_id' => 'manual_'.Str::uuid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'product_type' => $data['product_type'] ?? null,
            'vendor' => $workspace->name,
            'status' => 'active',
            'tags' => [],
        ]);

        $product->variants()->create([
            'title' => 'Default',
            'sku' => Str::upper(Str::random(8)),
            'price_cents' => $data['price_cents'],
            'inventory_qty' => $data['inventory_qty'],
            'currency' => $workspace->currency,
        ]);

        return redirect()->route('brand.onboarding')->with('status', "{$product->title} added.");
    }

    public function analyze(TenantContext $tenant, AnalyzeStore $analyze)
    {
        $workspace = $tenant->active();

        if (! $workspace->products()->exists()) {
            return back()->with('error', 'Add at least one product first.');
        }

        $analyze->run($workspace);

        return back()->with('status', 'AI analysis complete — hero products and niche detected.');
    }

    public function complete(TenantContext $tenant)
    {
        $workspace = $tenant->active();

        $workspace->update([
            'onboarding_step' => 'complete',
            'onboarding_completed_at' => now(),
        ]);

        return redirect()->route('brand.dashboard');
    }
}

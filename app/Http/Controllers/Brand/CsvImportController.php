<?php

namespace App\Http\Controllers\Brand;

use App\Domains\AI\Actions\AnalyzeStore;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CsvImportController extends Controller
{
    public function create()
    {
        return view('brand.products.import', [
            'sampleHeaders' => ['title', 'description', 'price', 'inventory', 'sku', 'product_type'],
        ]);
    }

    public function store(Request $request, TenantContext $tenant, AnalyzeStore $analyze)
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $workspace = $tenant->active();
        $channel = $workspace->channels()->firstOrCreate(
            ['type' => 'csv'],
            ['name' => 'CSV Import', 'status' => 'active']
        );

        $path = $request->file('csv')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $created = 0;
        $row = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (count($data) < count($header)) {
                $data = array_pad($data, count($header), null);
            }

            $record = array_combine($header, $data);
            $title = trim((string) ($record['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $product = Product::create([
                'uuid' => (string) Str::uuid(),
                'workspace_id' => $workspace->id,
                'channel_id' => $channel->id,
                'external_id' => 'csv_'.Str::uuid(),
                'title' => $title,
                'description' => $record['description'] ?? null,
                'product_type' => $record['product_type'] ?? null,
                'vendor' => $record['vendor'] ?? $workspace->name,
                'status' => 'active',
                'tags' => array_filter(array_map('trim', explode(',', (string) ($record['tags'] ?? '')))),
            ]);

            $product->variants()->create([
                'title' => 'Default',
                'sku' => $record['sku'] ?? Str::upper(Str::random(8)),
                'price_cents' => (int) round(((float) ($record['price'] ?? 0)) * 100),
                'inventory_qty' => (int) ($record['inventory'] ?? $record['inventory_qty'] ?? 0),
                'currency' => $workspace->currency,
            ]);

            $created++;
        }

        fclose($handle);

        if ($created > 0) {
            try {
                $analyze->run($workspace->fresh());
            } catch (\Throwable) {
                // Non-blocking.
            }
        }

        return redirect()->route('brand.products.index')
            ->with('status', "Imported {$created} products.");
    }
}

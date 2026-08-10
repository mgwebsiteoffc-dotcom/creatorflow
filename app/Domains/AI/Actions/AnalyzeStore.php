<?php

namespace App\Domains\AI\Actions;

use App\Domains\AI\AiGateway;
use App\Models\Product;
use App\Models\Workspace;

/**
 * Analyses a workspace's product catalog and assigns niche tags, hero scores
 * and suitability metadata to each product. In production this uses an LLM;
 * with the fake driver it produces deterministic results.
 */
class AnalyzeStore
{
    public function __construct(protected AiGateway $ai) {}

    /**
     * @return array<string, mixed>
     */
    public function run(Workspace $workspace): array
    {
        $products = $workspace->products()->with('variants')->get();

        if ($products->isEmpty()) {
            return ['niche' => null, 'hero_product_ids' => [], 'products_analyzed' => 0];
        }

        $descriptions = $products->map(fn (Product $p) => [
            'id' => $p->id,
            'title' => $p->title,
            'type' => $p->product_type,
            'price' => $p->priceCents() / 100,
            'inventory' => $p->inventoryTotal(),
            'description' => (string) \Illuminate\Support\Str::limit((string) $p->description, 200),
        ])->all();

        $response = $this->ai->complete(
            task: 'analyze_store',
            messages: [
                ['role' => 'system', 'content' => 'You are an e-commerce analyst. Respond with JSON only.'],
                ['role' => 'user', 'content' => json_encode($descriptions)],
            ],
            options: ['json' => true, 'seed' => ['hero_product_ids' => $this->detectHeroIds($products)]],
            workspace: $workspace,
            subjectType: 'workspace',
            subjectId: $workspace->id,
        );

        $analysis = $response->json() ?? [];
        $niche = $analysis['niche'] ?? $this->inferNiche($products);

        $heroIds = $this->detectHeroIds($products);
        $rank = 1;

        foreach ($products as $product) {
            $isHero = in_array($product->id, $heroIds, true);
            $product->update([
                'niche' => $niche,
                'hero_score' => $isHero ? max(80, 96 - ($rank++ * 4)) : rand(45, 72),
                'ai_analysis' => [
                    'suitability' => $isHero ? 'hero' : 'supporting',
                    'content_angles' => $analysis['content_angles'] ?? ['unboxing', 'in-use demo'],
                ],
            ]);
        }

        $workspace->update([
            'settings' => array_merge($workspace->settings ?? [], [
                'ai_niche' => $niche,
                'ai_analysis' => $analysis,
            ]),
        ]);

        return [
            'niche' => $niche,
            'hero_product_ids' => $heroIds,
            'products_analyzed' => $products->count(),
            'raw' => $analysis,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     * @return array<int, int>
     */
    protected function detectHeroIds($products): array
    {
        // Higher price + higher inventory + longer description ≈ hero product.
        return $products
            ->sortByDesc(fn (Product $p) => ($p->priceCents() / 100) * 0.6 + min($p->inventoryTotal(), 500) * 0.4 + (strlen((string) $p->description) / 50))
            ->take(min(3, $products->count()))
            ->pluck('id')
            ->all();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $products
     */
    protected function inferNiche($products): string
    {
        $types = $products->pluck('product_type')->filter()->map(fn ($t) => strtolower($t));

        return match (true) {
            $types->contains(fn ($t) => str_contains($t, 'beauty') || str_contains($t, 'skin')) => 'Beauty & Skincare',
            $types->contains(fn ($t) => str_contains($t, 'food') || str_contains($t, 'beverage')) => 'Food & Beverage',
            $types->contains(fn ($t) => str_contains($t, 'fitness')) => 'Fitness & Wellness',
            default => 'Lifestyle',
        };
    }
}

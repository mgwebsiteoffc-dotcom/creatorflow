<?php

namespace App\Domains\AI\Actions;

use App\Domains\AI\AiGateway;
use App\Models\Product;
use App\Models\Workspace;

/**
 * Produces a ready-to-launch campaign suggestion from a workspace's catalog.
 */
class GenerateCampaignSuggestion
{
    public function __construct(protected AiGateway $ai) {}

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function run(Workspace $workspace, array $overrides = []): array
    {
        $heroProducts = $workspace->products()
            ->orderByDesc('hero_score')
            ->with('variants')
            ->take(3)
            ->get();

        if ($heroProducts->isEmpty()) {
            return [];
        }

        $niche = $workspace->settings['ai_niche'] ?? 'Lifestyle';

        $response = $this->ai->complete(
            task: 'generate_campaign',
            messages: [
                ['role' => 'system', 'content' => 'You are a creator marketing strategist. Respond with JSON only.'],
                ['role' => 'user', 'content' => json_encode([
                    'niche' => $niche,
                    'products' => $heroProducts->map(fn (Product $p) => [
                        'id' => $p->id,
                        'title' => $p->title,
                        'price' => $p->priceCents() / 100,
                        'inventory' => $p->inventoryTotal(),
                        'hero_score' => $p->hero_score,
                    ]),
                ])],
            ],
            options: [
                'json' => true,
                'task' => 'generate_campaign',
                'seed' => array_merge([
                    'niche' => $niche,
                    'target_creators' => 30,
                    'type' => 'barter',
                ], $overrides),
            ],
            workspace: $workspace,
            subjectType: 'workspace',
            subjectId: $workspace->id,
        );

        $suggestion = $response->json() ?? [];

        $seedProducts = [];
        foreach ($heroProducts as $product) {
            $seedProducts[$product->id] = [
                'product_id' => $product->id,
                'variant_id' => $product->variants->first()?->id,
                'target_creators' => $this->allocationFor($product, $heroProducts, (int) ($suggestion['target_creators'] ?? 30)),
            ];
        }

        return array_merge($suggestion, [
            'niche' => $niche,
            'seed_products' => array_values($seedProducts),
            'brief' => $this->generateBrief($workspace, $heroProducts->first()),
            'predicted' => $suggestion['predicted'] ?? null,
        ]);
    }

    protected function generateBrief(Workspace $workspace, Product $product): string
    {
        $response = $this->ai->complete(
            task: 'generate_brief',
            messages: [
                ['role' => 'system', 'content' => 'Write a concise creator campaign brief in markdown.'],
                ['role' => 'user', 'content' => "Brand: {$workspace->name}. Product: {$product->title}. Niche: ".($workspace->settings['ai_niche'] ?? 'lifestyle')],
            ],
            options: ['task' => 'generate_brief', 'seed' => ['product_title' => $product->title]],
            workspace: $workspace,
            subjectType: 'product',
            subjectId: $product->id,
        );

        return $response->text;
    }

    protected function allocationFor(Product $product, $heroProducts, int $total): int
    {
        $weight = max(1, (int) round(((float) $product->hero_score) / 10));
        $totalWeight = (int) $heroProducts->sum(fn (Product $p) => max(1, (int) round(((float) $p->hero_score) / 10)));

        return max(1, (int) round($total * ($weight / max(1, $totalWeight))));
    }
}

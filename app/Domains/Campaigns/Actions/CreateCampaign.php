<?php

namespace App\Domains\Campaigns\Actions;

use App\Models\Campaign;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCampaign
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array{product_id:int, variant_id?:?int, target_creators:int, fee_cents?:int, commission_rate?:?float}>  $products
     */
    public function handle(Workspace $workspace, array $data, array $products, ?int $userId = null): Campaign
    {
        return DB::transaction(function () use ($workspace, $data, $products, $userId) {
            $targetCreators = array_sum(array_column($products, 'target_creators'));

            $assumedRate = (float) ($data['acceptance_rate_assumed'] ?? 30);
            $invitePool = (int) ceil($targetCreators / max(1, $assumedRate / 100));

            $campaign = Campaign::create([
                'uuid' => (string) Str::uuid(),
                'workspace_id' => $workspace->id,
                'created_by' => $userId,
                'title' => $data['title'],
                'type' => $data['type'] ?? 'barter',
                'status' => 'draft',
                'niche' => $data['niche'] ?? null,
                'summary' => $data['summary'] ?? null,
                'brief' => $data['brief'] ?? null,
                'objectives' => $data['objectives'] ?? ['ugc'],
                'content_types' => $data['content_types'] ?? ['video'],
                'deliverables' => $data['deliverables'] ?? null,
                'usage_rights' => $data['usage_rights'] ?? null,
                'audience_criteria' => $data['audience_criteria'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'budget_total_cents' => $data['budget_total_cents'] ?? 0,
                'budget_currency' => $workspace->currency,
                'creator_fee_cents' => $data['creator_fee_cents'] ?? 0,
                'commission_rate' => $data['commission_rate'] ?? 0,
                'target_creators' => $targetCreators,
                'invite_pool_size' => $invitePool,
                'acceptance_rate_assumed' => $assumedRate,
                'waitlist_size' => $data['waitlist_size'] ?? (int) ceil($invitePool * 0.2),
                'ai_generated' => (bool) ($data['ai_generated'] ?? false),
                'ai_predicted_roi' => $data['ai_predicted_roi'] ?? null,
                'ai_metadata' => $data['ai_metadata'] ?? null,
            ]);

            foreach ($products as $p) {
                $campaign->products()->create([
                    'product_id' => $p['product_id'],
                    'variant_id' => $p['variant_id'] ?? null,
                    'target_creators' => $p['target_creators'],
                    'fee_cents' => $p['fee_cents'] ?? 0,
                    'commission_rate' => $p['commission_rate'] ?? null,
                ]);
            }

            return $campaign->load('products');
        });
    }
}

<?php

namespace App\Console\Commands;

use App\Domains\AI\AiGateway;
use App\Models\Creator;
use App\Models\PlatformSetting;
use Illuminate\Console\Command;

class ScanCreatorFraud extends Command
{
    protected $signature = 'creators:scan-fraud
                            {--limit=50 : Max creators to score in one run}
                            {--force : Ignore the feature flag}';
    protected $description = 'Nightly AI scan that updates creator fraud_risk + fake_follower_pct.';

    public function handle(AiGateway $ai): int
    {
        if (! $this->option('force') && ! PlatformSetting::feature('fraud_scan')) {
            $this->warn('Feature flag "fraud_scan" is off. Enable it in Admin → Integrations → Feature flags. (Use --force to bypass.)');
            return self::SUCCESS;
        }

        $limit = (int) $this->option('limit');
        $creators = Creator::query()
            ->where('status', 'active')
            ->orderBy('updated_at')
            ->take($limit)
            ->get();

        $scanned = 0;
        foreach ($creators as $creator) {
            try {
                $response = $ai->complete(
                    task: 'fraud_check',
                    messages: [
                        ['role' => 'system', 'content' => 'You detect follower fraud + engagement anomalies for creator marketing platforms. Reply with JSON only.'],
                        ['role' => 'user',   'content' => "Creator: {$creator->display_name} · {$creator->follower_count_total} followers · {$creator->engagement_rate}% ER · city {$creator->city}. Return: {fraud_risk:0-100, fake_follower_pct:0-100, verdict:'low_risk'|'watch'|'high_risk', signals:[…]}"],
                    ],
                    options: ['json' => true, 'task' => 'fraud_check', 'seed' => ['handle' => $creator->slug]],
                );

                $parsed = is_array($response->structured ?? null) ? $response->structured : json_decode($response->text, true);
                if (is_array($parsed)) {
                    $creator->update([
                        'fraud_risk' => min(100, max(0, (float) ($parsed['fraud_risk'] ?? $creator->fraud_risk))),
                    ]);
                    $scanned++;
                }
            } catch (\Throwable $e) {
                $this->warn("Failed for {$creator->display_name}: {$e->getMessage()}");
            }
        }

        $this->info("✓ Scanned {$scanned} creators.");
        return self::SUCCESS;
    }
}

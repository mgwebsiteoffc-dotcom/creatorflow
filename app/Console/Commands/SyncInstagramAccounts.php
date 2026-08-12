<?php

namespace App\Console\Commands;

use App\Models\CreatorSocialAccount;
use App\Models\PlatformSetting;
use App\Support\InstagramGraphService;
use Illuminate\Console\Command;

class SyncInstagramAccounts extends Command
{
    protected $signature = 'instagram:sync {--limit=100 : Max accounts to sync per run}';
    protected $description = 'Refresh follower + engagement rate for every creator with a connected IG Business account.';

    public function handle(InstagramGraphService $ig): int
    {
        if (! PlatformSetting::current()->instagram_enabled) {
            $this->warn('Instagram integration is disabled. Enable it in Admin → Integrations.');
            return self::SUCCESS;
        }

        $accounts = CreatorSocialAccount::whereNotNull('graph_ig_user_id')
            ->whereNotNull('graph_access_token')
            ->where(function ($q) {
                $q->whereNull('graph_synced_at')->orWhere('graph_synced_at', '<', now()->subHours(20));
            })
            ->orderBy('graph_synced_at')
            ->take((int) $this->option('limit'))
            ->get();

        $done = 0;
        foreach ($accounts as $a) {
            $r = $ig->syncAccount($a);
            if (! empty($r['ok'])) $done++;
        }
        $this->info("✓ Synced {$done}/{$accounts->count()} Instagram accounts.");
        return self::SUCCESS;
    }
}

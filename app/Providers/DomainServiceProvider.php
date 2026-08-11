<?php

namespace App\Providers;

use App\Domains\AI\AiGateway;
use App\Domains\AI\Contracts\AiProvider;
use App\Domains\AI\Providers\FakeAiProvider;
use App\Domains\AI\Providers\OpenAiProvider;
use App\Domains\Commerce\Channels\ChannelRegistry;
use App\Domains\Commerce\Channels\ManualChannel;
use App\Domains\Commerce\Channels\Shopify\ShopifyChannel;
use App\Support\SchemaCheck;
use App\Support\TenantContext;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);

        $this->app->singleton(ChannelRegistry::class, function () {
            $registry = new ChannelRegistry();
            $registry->register('shopify', ShopifyChannel::class);
            $registry->register('manual', ManualChannel::class);
            $registry->register('csv', ManualChannel::class);
            $registry->register('api', ManualChannel::class);

            return $registry;
        });

        // Resolve AI provider from admin-managed settings, with env fallback so
        // the app never boots into a broken state.
        $this->app->singleton(AiProvider::class, function () {
            [$driver, $key, $model, $embed, $baseUrl] = $this->resolveAiConfig();

            if ($driver === 'openai' && $key) {
                return new OpenAiProvider($key, $model, $embed, timeout: 30, baseUrl: $baseUrl);
            }

            return new FakeAiProvider();
        });

        $this->app->singleton(AiGateway::class, function ($app) {
            return new AiGateway($app->make(AiProvider::class));
        });
    }

    public function boot(): void
    {
        //
    }

    /**
     * Prefer DB-managed AI settings; fall back to env.
     *
     * @return array{0:string,1:?string,2:string,3:string,4:?string}
     */
    protected function resolveAiConfig(): array
    {
        $driver  = (string) config('creatorplex.ai.driver', 'fake');
        $key     = config('creatorplex.ai.openai.key');
        $model   = (string) config('creatorplex.ai.openai.model', 'gpt-4o-mini');
        $embed   = (string) config('creatorplex.ai.openai.embedding_model', 'text-embedding-3-small');
        $baseUrl = null;

        try {
            if (SchemaCheck::has('platform_settings')) {
                $row = \App\Models\PlatformSetting::query()->first();
                if ($row) {
                    if (! empty($row->ai_driver))     $driver = (string) $row->ai_driver;
                    if (! empty($row->ai_openai_key)) $key    = (string) $row->ai_openai_key;
                    if (! empty($row->ai_openai_model))           $model = (string) $row->ai_openai_model;
                    if (! empty($row->ai_openai_embedding_model)) $embed = (string) $row->ai_openai_embedding_model;
                    if (! empty($row->ai_base_url))               $baseUrl = (string) $row->ai_base_url;
                }
            }
        } catch (\Throwable $e) {
            // Never fail app boot because settings table is missing / DB down.
        }

        return [$driver, $key, $model, $embed, $baseUrl];
    }
}

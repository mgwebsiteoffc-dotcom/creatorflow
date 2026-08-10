<?php

namespace App\Providers;

use App\Domains\AI\AiGateway;
use App\Domains\AI\Contracts\AiProvider;
use App\Domains\AI\Providers\FakeAiProvider;
use App\Domains\AI\Providers\OpenAiProvider;
use App\Domains\Commerce\Channels\ChannelRegistry;
use App\Domains\Commerce\Channels\ManualChannel;
use App\Domains\Commerce\Channels\Shopify\ShopifyChannel;
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

        $this->app->singleton(AiProvider::class, function () {
            $driver = config('creatorflow.ai.driver', 'fake');

            return match ($driver) {
                'openai' => new OpenAiProvider(
                    (string) config('creatorflow.ai.openai.key'),
                    (string) config('creatorflow.ai.openai.model'),
                    (string) config('creatorflow.ai.openai.embedding_model'),
                ),
                default => new FakeAiProvider(),
            };
        });

        $this->app->singleton(AiGateway::class, function ($app) {
            return new AiGateway($app->make(AiProvider::class));
        });
    }

    public function boot(): void
    {
        //
    }
}

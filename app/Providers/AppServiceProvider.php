<?php

namespace App\Providers;

use App\View\Composers\NotificationComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(DomainServiceProvider::class);
    }

    public function boot(): void
    {
        // Strict mode in dev surfaces N+1 queries and missing attributes during
        // development, but lazy-loading is allowed in production.
        Model::shouldBeStrict(! app()->isProduction());
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            if (app()->isProduction()) {
                report(new \RuntimeException("Lazy loading [{$relation}] on [".get_class($model).']'));
            }
        });

        View::composer('partials.topbar', NotificationComposer::class);
    }
}

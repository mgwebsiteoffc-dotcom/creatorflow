<?php

namespace App\Providers;

use App\View\Composers\HomepageComposer;
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
        // Strict mode in dev surfaces N+1 queries. We don't want it to throw
        // when a model attribute hasn't been migrated yet or a select() left
        // some columns off — those cases are handled with $attributes defaults
        // on the models. Only lazy-loading violations stay strict.
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventAccessingMissingAttributes(false);
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            if (app()->isProduction()) {
                report(new \RuntimeException("Lazy loading [{$relation}] on [".get_class($model).']'));
            }
        });

        View::composer('partials.topbar', NotificationComposer::class);
        View::composer('welcome', HomepageComposer::class);
    }
}

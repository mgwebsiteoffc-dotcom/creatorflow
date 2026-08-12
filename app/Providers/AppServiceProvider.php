<?php

namespace App\Providers;

use App\View\Composers\HomepageComposer;
use App\View\Composers\NotificationComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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

        // Use Tailwind pagination markup + our own view for a branded look.
        Paginator::defaultView('pagination.creatorplex');
        Paginator::defaultSimpleView('pagination.creatorplex-simple');

        // Blade sugar: @money(cents) and @moneyCompact(cents) — always resolves
        // the current workspace currency, defaults to INR when none is set.
        Blade::directive('money', function ($expr) {
            return "<?php echo \\App\\Support\\Money::fmt($expr); ?>";
        });
        Blade::directive('moneyCompact', function ($expr) {
            return "<?php echo \\App\\Support\\Money::fmtCompact($expr); ?>";
        });
    }
}

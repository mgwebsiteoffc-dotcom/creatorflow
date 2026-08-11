<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/brand';

    public function boot(): void
    {
        // Every model in this app uses HasUuid → getRouteKeyName() = 'uuid'.
        // Accept either a numeric id (older routes / manual URLs) OR a full uuid.
        $idOrUuid = '[0-9a-f\-]{36}|[0-9]+';

        Route::pattern('workspace',  $idOrUuid);
        Route::pattern('user',       $idOrUuid);
        Route::pattern('campaign',   $idOrUuid);
        Route::pattern('product',    $idOrUuid);
        Route::pattern('creator',    $idOrUuid);
        Route::pattern('assignment', $idOrUuid);
        Route::pattern('submission', $idOrUuid);
        Route::pattern('invitation', $idOrUuid);
        Route::pattern('lead',       $idOrUuid);
        Route::pattern('post',       $idOrUuid);
        Route::pattern('payout',     $idOrUuid);
    }
}

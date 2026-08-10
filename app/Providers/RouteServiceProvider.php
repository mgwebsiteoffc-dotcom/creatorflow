<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/brand';

    public function boot(): void
    {
        Route::pattern('workspace', '[0-9]+');
        Route::pattern('campaign', '[0-9a-f\-]{36}');
        Route::pattern('product', '[0-9a-f\-]{36}');
        Route::pattern('creator', '[0-9a-f\-]{36}');
        Route::pattern('assignment', '[0-9a-f\-]{36}');
        Route::pattern('submission', '[0-9a-f\-]{36}');
    }
}

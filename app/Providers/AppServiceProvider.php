<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;

ini_set('memory_limit', '-1');

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);

        // Passport::tokensExpireIn(now()->addMinutes(1));
        // Passport::refreshTokensExpireIn(now()->addDays(7));
    }
}

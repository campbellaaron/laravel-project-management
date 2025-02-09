<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Set the timezone dynamically for authenticated users
        if (Auth::check()) {
            config(['app.timezone' => Auth::user()->timezone]);
            date_default_timezone_set(Auth::user()->timezone);
        }
    }
}

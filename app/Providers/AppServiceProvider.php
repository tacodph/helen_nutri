<?php

namespace App\Providers;

use App\Models\Briefing;
use App\Observers\BriefingObserver;
use Illuminate\Support\ServiceProvider;

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
        Briefing::observe(BriefingObserver::class);
    }
}

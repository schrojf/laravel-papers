<?php

namespace App\Providers;

use App\Papers\SimplePaper;
use Illuminate\Support\ServiceProvider;
use Schrojf\Papers\Papers;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Papers::register([
            SimplePaper::class,
        ]);
    }
}

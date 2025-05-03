<?php

namespace Schrojf\Papers;

use Illuminate\Support\ServiceProvider;
use Schrojf\Papers\Commands\PaperCommand;

class PaperServiceProvider extends ServiceProvider
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
        //

        if ($this->app->runningInConsole()) {
            $this->commands([
                PaperCommand::class,
            ]);
        }
    }
}

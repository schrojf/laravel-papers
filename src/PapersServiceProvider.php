<?php

namespace Schrojf\Papers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Schrojf\Papers\Commands\PapersCommand;

class PapersServiceProvider extends ServiceProvider
{
    /**
     *  All of the Package event / listener mappings.
     *
     * @var array<class-string, array<class-string>>
     */
    protected array $events = [
        // Events\Event::class => [
        //     Listeners\Listener::class,
        // ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/papers.php', 'papers');

        // $this->app->singleton(Papers::class, fn () => new Papers);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerEvents();
        $this->registerRoutes();
        $this->registerResources();

        if ($this->app->runningInConsole()) {
            $this->registerPublications();
            $this->registerMigrations();
            $this->registerAssets();
            $this->registerCommands();
        }
    }

    /**
     * Register the Package job events.
     */
    private function registerEvents(): void
    {
        $events = $this->app->make(Dispatcher::class);

        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * Register the Package routes.
     */
    private function registerRoutes(): void
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        Route::group([
            'domain' => config('papers.domain', null),
            'prefix' => config('papers.path'),
            'namespace' => 'Schrojf\Papers\Http\Controllers',
            'middleware' => config('papers.middleware', 'web'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        Route::group([
            'domain' => config('papers.domain', null),
            'prefix' => Str::finish(config('papers.path', ''), '/').'api',
            'namespace' => 'Schrojf\Papers\Http\Controllers\Api',
            'middleware' => config('papers.api_middleware', 'api'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * Register the Package resources.
     */
    private function registerResources(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../lang', 'papers');
        // $this->loadJsonTranslationsFrom(__DIR__.'/../lang');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'papers');
    }

    /**
     * Setup the resource publishing groups for Package.
     */
    private function registerPublications(): void
    {
        $this->publishes([
            __DIR__.'/../config/papers.php' => $this->app->configPath('papers.php'),
        ], ['papers', 'papers-config']);

        //     $this->publishes([
        //         __DIR__.'/../lang' => $this->app->langPath('vendor/papers'),
        //     ]);

        //     $this->publishes([
        //         __DIR__.'/../resources/views' => resource_path('views/vendor/papers'),
        //     ]);
    }

    private function registerMigrations(): void
    {
        $method = method_exists($this, 'publishesMigrations') ? 'publishesMigrations' : 'publishes';

        $this->{$method}([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['papers', 'papers-migrations']);
    }

    private function registerAssets(): void
    {
        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/papers'),
        ], [/* 'public', */ 'papers', 'papers-assets']);
    }

    /**
     * Register the Package Artisan commands.
     */
    private function registerCommands(): void
    {
        $this->commands([
            PapersCommand::class,
        ]);
    }
}

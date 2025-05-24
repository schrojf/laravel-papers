<?php

namespace Tests;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Schrojf\Papers\Http\Middleware\AuthorizePapers;
use Schrojf\Papers\PapersServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Database\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            PapersServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        tap($app['config'], function (Repository $config) {
            $config->set('database.default', 'testing');

            // Remove AuthorizePapers middleware from config
            $config->set('papers.middleware', array_filter(
                $config->get('papers.middleware', []),
                fn ($middleware) => $middleware !== AuthorizePapers::class
            ));

            $config->set('papers.api_middleware', array_filter(
                $config->get('papers.api_middleware', []),
                fn ($middleware) => $middleware !== AuthorizePapers::class
            ));
        });

        /*
        $migration = include __DIR__.'/../database/migrations/create_papers_table.php';
        $migration->up();
        */
    }
}

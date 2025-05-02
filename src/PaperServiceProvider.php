<?php

namespace Schrojf\Paper;

use Schrojf\Paper\Commands\PaperCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PaperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-papers')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-papers_table')
            ->hasCommand(PaperCommand::class);
    }
}

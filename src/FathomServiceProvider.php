<?php

namespace Based\Fathom;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Based\Fathom\Commands\FathomCommand;

class FathomServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('fathom')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_fathom_table')
            ->hasCommand(FathomCommand::class);
    }
}

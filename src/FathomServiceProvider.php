<?php

namespace Based\Fathom;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FathomServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->bind(Fathom::class, function ($app) {
            return new Fathom(config('fathom.token'));
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('fathom')
            ->hasConfigFile();
    }
}

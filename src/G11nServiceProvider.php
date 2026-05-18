<?php

declare(strict_types=1);

namespace Victormgomes\G11n;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Victormgomes\G11n\Services\GlobalizationService;

class G11nServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('g11n')
            ->hasConfigFile()
            ->hasMigration('create_global_preferences_table');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(G11n::class, function () {
            return new G11n;
        });

        $this->app->singleton(GlobalizationService::class, function () {
            return new GlobalizationService;
        });
    }
}

<?php

namespace Noouh\MultipleMigrations\Providers;

use Illuminate\Support\ServiceProvider;
use Noouh\MultipleMigrations\Console\Commands\MigrateMultiplePaths;

class MultipleMigrationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            MigrateMultiplePaths::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

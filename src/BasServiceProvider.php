<?php

namespace ShikhBas\BasLaravelSdk;

use Illuminate\Support\ServiceProvider;
use ShikhBas\BasLaravelSdk\Services\BasService;

use ShikhBas\BasLaravelSdk\Facades\Bas as BasFacade;


class BasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/bas.php', 'bas'
        );

        $this->app->singleton(BasService::class, function ($app) {
            return new BasService(config('bas'));
        });


        // Facades - Binding to Class Names
        $this->app->bind('bas', function ($app) {
            return $app->make(BasService::class);
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/bas.php' => config_path('bas.php'),
        ], 'bas-config');
    }
}
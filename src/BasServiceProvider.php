<?php

namespace ShikhBas\BasLaravelSdk;

use Illuminate\Support\ServiceProvider;
use ShikhBas\BasLaravelSdk\Services\BasService;
use ShikhBas\BasLaravelSdk\Services\AuthService;
use ShikhBas\BasLaravelSdk\Services\PaymentService;
use ShikhBas\BasLaravelSdk\Facades\Bas as BasFacade;
use ShikhBas\BasLaravelSdk\Facades\AuthBas as AuthBasFacade;
use ShikhBas\BasLaravelSdk\Facades\PaymentBas as PaymentBasFacade;

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

        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(BasService::class), config('bas'));
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(BasService::class), config('bas'));
        });


        // Facades - Binding to Class Names
        $this->app->bind('bas', function ($app) {
            return $app->make(BasService::class);
        });
        $this->app->bind('authbas', function ($app) {
            return $app->make(AuthService::class);
        });
        $this->app->bind('paymentbas', function ($app) {
            return $app->make(PaymentService::class);
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
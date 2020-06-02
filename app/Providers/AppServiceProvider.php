<?php

namespace App\Providers;

use App\Http\Services\ExchangeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Http\Services\GenericInterfaces\ExchangeServiceInterface', function($app) {
            return new ExchangeService(env('EXCHANGE_SERVICE_URL'), env('EXCHANGE_SERVICE_TIMEOUT'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Helpers\Classes\AuthTokenUtilityHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ResponseFactory::class, function() {
            return new \Laravel\Lumen\Http\ResponseFactory();
        });

        $this->app->singleton('auth_token_utility', AuthTokenUtilityHandler::class);
    }

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}

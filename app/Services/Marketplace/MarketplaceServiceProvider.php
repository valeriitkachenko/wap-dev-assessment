<?php


namespace App\Services\Marketplace;

use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('marketplace', function () {
            return new MarketplaceService(config('marketplace'));
        });
    }
}

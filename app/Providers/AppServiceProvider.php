<?php

namespace App\Providers;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\GatewayConfigurationRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\PurchaseRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TransactionRepository;
use App\Interfaces\TransactionRepositoryInterface;
use App\Repositories\ClientRepository;
use App\Repositories\GatewayRepositoryConfiguration;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(GatewayConfigurationRepositoryInterface::class, GatewayRepositoryConfiguration::class);
        $this->app->bind(PurchaseRepositoryInterface::class, PurchaseRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
<?php

namespace App\Providers;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TransactionRepository;
use App\Interfaces\TransactionRepositoryInterface;
use App\Repositories\ProductRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
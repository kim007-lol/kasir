<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        \Carbon\Carbon::setLocale('id');

        \App\Models\WarehouseItem::observe(\App\Observers\WarehouseItemObserver::class);
        \App\Models\CashierItem::observe(\App\Observers\CashierItemObserver::class);
    }
}

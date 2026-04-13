<?php

namespace App\Providers;

use App\Models\Container;
use App\Models\ContainerExpense;
use App\Models\ReceiptItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        Route::bind('receiptItem', function (string $value, \Illuminate\Routing\Route $route) {
            $receiptItem = ReceiptItem::where('id', $value)->firstOrFail();
            $container = $route->parameter('container');
            $containerId = $container instanceof Container ? $container->id : (int) $container;
            abort_unless((int) $receiptItem->container_id === $containerId, 404);

            return $receiptItem;
        });

        Route::bind('expense', function (string $value, \Illuminate\Routing\Route $route) {
            $expense = ContainerExpense::where('id', $value)->firstOrFail();
            $container = $route->parameter('container');
            $containerId = $container instanceof Container ? $container->id : (int) $container;
            abort_unless((int) $expense->container_id === $containerId, 404);

            return $expense;
        });
    }
}

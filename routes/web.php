<?php

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\ContainerExpenseController;
use App\Http\Controllers\ContainerReceiptItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\WarehouseReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', 'tenant'])->group(function () {
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', function () {
        return \Inertia\Inertia::render('Settings');
    })->name('settings');

    Route::resource('suppliers', SupplierController::class);
    Route::get('containers/{container}/purchases', [ContainerController::class, 'purchases'])->name('containers.purchases');
    Route::get('containers/{container}/sales', [ContainerController::class, 'sales'])->name('containers.sales');
    Route::get('containers/{container}/stock', [ContainerController::class, 'stock'])->name('containers.stock');
    Route::resource('containers', ContainerController::class);
    Route::post('containers/{container}/receipt-items', [ContainerReceiptItemController::class, 'store'])->name('containers.receipt-items.store');
    Route::put('containers/{container}/receipt-items/{receiptItem}', [ContainerReceiptItemController::class, 'update'])->name('containers.receipt-items.update');
    Route::delete('containers/{container}/receipt-items/{receiptItem}', [ContainerReceiptItemController::class, 'destroy'])->name('containers.receipt-items.destroy');
    Route::post('containers/{container}/expenses', [ContainerExpenseController::class, 'store'])->name('containers.expenses.store');
    Route::put('containers/{container}/expenses/{expense}', [ContainerExpenseController::class, 'update'])->name('containers.expenses.update');
    Route::delete('containers/{container}/expenses/{expense}', [ContainerExpenseController::class, 'destroy'])->name('containers.expenses.destroy');
    Route::get('containers/{container}/supplier-payments', [SupplierPaymentController::class, 'index'])->name('containers.supplier-payments.index');
    Route::post('supplier-payments', [SupplierPaymentController::class, 'store'])->name('supplier-payments.store');
    Route::delete('supplier-payments/{supplierPayment}', [SupplierPaymentController::class, 'destroy'])->name('supplier-payments.destroy');

    Route::resource('warehouse-receipts', WarehouseReceiptController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');

    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);
    Route::post('sales/{sale}/confirm', [SaleController::class, 'confirm'])->name('sales.confirm');
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::get('sales/{sale}/customer-payments', [CustomerPaymentController::class, 'index'])->name('sales.customer-payments.index');
    Route::post('customer-payments', [CustomerPaymentController::class, 'store'])->name('customer-payments.store');
    Route::delete('customer-payments/{customerPayment}', [CustomerPaymentController::class, 'destroy'])->name('customer-payments.destroy');

    Route::get('reports/container/{container}', [ReportController::class, 'containerSummary'])->name('reports.container-summary');
    Route::get('reports/customer-balance', [ReportController::class, 'customerBalance'])->name('reports.customer-balance');
    Route::get('reports/supplier-balance', [ReportController::class, 'supplierBalance'])->name('reports.supplier-balance');
    Route::get('reports/profit', [ReportController::class, 'profitReport'])->name('reports.profit');
});

Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $locale = $request->validate(['locale' => 'required|in:ar,en'])['locale'];

    return redirect()->back()->cookie('contistock_locale', $locale, 60 * 24 * 365);
})->name('locale.update');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SalesReturnController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

// cashier@gmail.com / password
// admin@gmail.com / password
// ১. homepage when have login
Route::get('/', function () {
    return redirect()->route('login');
});

// dashboard when other page have login
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // product route
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    // --- Product Routes ---
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');


    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update'); // 

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/search-by-barcode', [ProductController::class, 'searchByBarcode']);

    // perches route
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    // POS route
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/orders', [PosController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}/print', [PosController::class, 'printInvoice'])->name('orders.print');

    // reports route
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');

    // profile (Breeze Default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/products/search-by-barcode', [ProductController::class, 'searchByBarcode']);
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers/payment', [CustomerController::class, 'storePayment'])->name('customers.payment');
});
Route::group(['middleware' => ['role:Super Admin|Manager']], function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/reports/profit', [ReportController::class, 'profitReport'])->name('reports.profit');

    Route::get('/returns/create', [SalesReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns/search', [SalesReturnController::class, 'search'])->name('returns.search');
    Route::post('/returns/store', [SalesReturnController::class, 'store'])->name('returns.store');

    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'bn', 'hi', 'ur', 'zh', 'es'])) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    })->name('lang.switch');
});


require __DIR__ . '/auth.php';

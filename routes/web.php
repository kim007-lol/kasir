<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;

// Redirect home ke login jika belum auth, ke dashboard jika sudah
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Login & Register Routes (tanpa laravel/ui)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::resource('members', MemberController::class);

    // New routes for warehouse system
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::patch('/suppliers/{id}/restore', [\App\Http\Controllers\SupplierController::class, 'restore'])->name('suppliers.restore');
    Route::resource('warehouse', \App\Http\Controllers\WarehouseController::class);
    Route::resource('cashier-items', \App\Http\Controllers\CashierItemController::class);

    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transaksi/tambah', [TransactionController::class, 'addToCart'])->name('transactions.addToCart');
    Route::post('/transaksi/tambah-banyak', [TransactionController::class, 'addMultipleToCart'])->name('transactions.addMultipleToCart');
    Route::delete('/transaksi/hapus/{itemId}', [TransactionController::class, 'removeFromCart'])->name('transactions.removeFromCart');
    Route::post('/transaksi/bayar', [TransactionController::class, 'checkout'])->name('transactions.checkout');
    Route::get('/transaksi/struk', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('/transaksi/download/{id}', [TransactionController::class, 'downloadReceipt'])->name('transactions.downloadReceipt');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{transaction}', [HistoryController::class, 'show'])->name('history.show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');
    Route::get('/reports/stock-entries', [ReportController::class, 'stockEntriesHistory'])->name('reports.stockEntries');
});

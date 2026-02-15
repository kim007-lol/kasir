<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\CashierItemController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect home based on role
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'kasir') {
            return redirect()->route('cashier.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Login Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Warehouse routes (explicitly defined before resource to allow specific overrides/additions)
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/status', [WarehouseController::class, 'getStockStatus'])->name('warehouse.status');
    Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
    Route::resource('warehouse', WarehouseController::class)->except(['index', 'create']); // Use resource for other actions

    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::resource('members', MemberController::class);

    Route::resource('suppliers', SupplierController::class);
    Route::patch('/suppliers/{id}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');

    // Cashier Item Management (Admin Side)
    Route::resource('cashier-items', CashierItemController::class);

    // Admin Transaction Features
    // Admin Transaction Features - REMOVED as per request
    // Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    // ... (routes removed)
    // Keep History and Reports

    // Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    // Route::post('/transaksi/tambah', [TransactionController::class, 'addToCart'])->name('transactions.addToCart');
    // Route::post('/transaksi/tambah-banyak', [TransactionController::class, 'addMultipleToCart'])->name('transactions.addMultipleToCart');
    // Route::delete('/transaksi/hapus/{itemId}', [TransactionController::class, 'removeFromCart'])->name('transactions.removeFromCart');
    // Route::post('/transaksi/bayar', [TransactionController::class, 'checkout'])->name('transactions.checkout');
    // Route::get('/transaksi/struk', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('/transaksi/download/{id}', [TransactionController::class, 'downloadReceipt'])->name('transactions.downloadReceipt');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{transaction}', [HistoryController::class, 'show'])->name('history.show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');
    Route::get('/reports/stock-entries', [ReportController::class, 'stockEntriesHistory'])->name('reports.stockEntries');
    Route::get('/reports/transfer-history', [ReportController::class, 'transferHistory'])->name('reports.transferHistory');
    Route::resource('users', UserController::class)->only(['index', 'create', 'store']);
    Route::get('/about', [BusinessProfileController::class, 'index'])->name('about');
});

// CASHIER ROUTES
Route::middleware(['auth', 'role:kasir'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', function () {
        return view('cashier.dashboard');
    })->name('dashboard');

    // Transaksi (Reusing TransactionController)
    // We intentionally share route names (except prefix) if possible, but existing views verify permissions/roles?
    // No, reusing logic is fine.

    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transaksi/tambah', [TransactionController::class, 'addToCart'])->name('transactions.addToCart');
    Route::post('/transaksi/tambah-banyak', [TransactionController::class, 'addMultipleToCart'])->name('transactions.addMultipleToCart');
    Route::delete('/transaksi/hapus/{itemId}', [TransactionController::class, 'removeFromCart'])->name('transactions.removeFromCart');
    Route::post('/transaksi/bayar', [TransactionController::class, 'checkout'])->name('transactions.checkout');
    Route::get('/transaksi/struk', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('/transaksi/download/{id}', [TransactionController::class, 'downloadReceipt'])->name('transactions.downloadReceipt');

    // Stok Item Kasir
    // Using a separate method/view for Cashier's stock management to handle consignment
    Route::get('/stock', [CashierItemController::class, 'cashierIndex'])->name('stock.index');
    Route::get('/stock/status', [CashierItemController::class, 'getStockStatus'])->name('stock.status');
    Route::post('/stock/warehouse', [CashierItemController::class, 'storeFromWarehouse'])->name('stock.storeFromWarehouse');
    Route::post('/stock/consignment', [CashierItemController::class, 'storeConsignment'])->name('stock.storeConsignment');

    // Barang Titipan (Consignment) - Dedicated Page
    Route::get('/consignment', [CashierItemController::class, 'consignmentIndex'])->name('consignment.index');
    Route::post('/consignment', [CashierItemController::class, 'storeConsignment'])->name('consignment.store');
    Route::get('/consignment/{cashierItem}/edit', [CashierItemController::class, 'editConsignment'])->name('consignment.edit');
    Route::put('/consignment/{cashierItem}', [CashierItemController::class, 'updateConsignment'])->name('consignment.update');
    Route::delete('/consignment/{cashierItem}', [CashierItemController::class, 'destroyConsignment'])->name('consignment.destroy');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{transaction}', [HistoryController::class, 'show'])->name('history.show');
});

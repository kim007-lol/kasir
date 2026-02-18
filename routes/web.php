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
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashierBookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== LANDING PAGE =====
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'kasir' => redirect()->route('cashier.dashboard'),
            'pelanggan' => redirect()->route('booking.menu'),
            default => redirect()->route('dashboard'),
        };
    }
    return view('landing');
})->name('landing');

// ===== STAFF AUTH (Admin/Kasir) =====
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');
Route::get('/staff/login', function () {
    return view('auth.login');
})->name('staff.login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===== PELANGGAN AUTH =====
Route::prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit')->middleware('guest');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit')->middleware('guest');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// ===== BOOKING ROUTES (Pelanggan) =====
Route::middleware(['auth', 'role:pelanggan'])->prefix('booking')->name('booking.')->group(function () {
    Route::get('/menu', [BookingController::class, 'menu'])->name('menu');
    Route::post('/cart/add', [BookingController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [BookingController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{index}', [BookingController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/cart', [BookingController::class, 'cart'])->name('cart');
    Route::get('/checkout', [BookingController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [BookingController::class, 'placeOrder'])->name('placeOrder');
    Route::get('/status/{booking}', [BookingController::class, 'status'])->name('status');
    Route::get('/history', [BookingController::class, 'history'])->name('history');
    // API: check order status (polling from status page)
    Route::get('/api/status/{booking}', [BookingController::class, 'apiStatus'])->name('api.status');
});

// ===== ADMIN ROUTES =====
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Warehouse routes
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/status', [WarehouseController::class, 'getStockStatus'])->name('warehouse.status');
    Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
    Route::resource('warehouse', WarehouseController::class)->except(['index', 'create']);

    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::resource('members', MemberController::class);
    Route::post('/members/{id}/restore', [MemberController::class, 'restore'])->name('members.restore');

    Route::resource('suppliers', SupplierController::class);
    Route::patch('/suppliers/{id}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');

    // Cashier Item Management (Admin Side)
    Route::resource('cashier-items', CashierItemController::class);

    Route::get('/transaksi/download/{id}', [TransactionController::class, 'downloadReceipt'])->name('transactions.downloadReceipt');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{transaction}', [HistoryController::class, 'show'])->name('history.show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');
    Route::get('/reports/stock-entries', [ReportController::class, 'stockEntriesHistory'])->name('reports.stockEntries');
    Route::get('/reports/transfer-history', [ReportController::class, 'transferHistory'])->name('reports.transferHistory');
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::get('/about', [BusinessProfileController::class, 'index'])->name('about');
});

// ===== CASHIER ROUTES =====
Route::middleware(['auth', 'role:kasir'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', function () {
        return view('cashier.dashboard');
    })->name('dashboard');

    // Transaksi
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transaksi/tambah', [TransactionController::class, 'addToCart'])->name('transactions.addToCart');
    Route::post('/transaksi/tambah-banyak', [TransactionController::class, 'addMultipleToCart'])->name('transactions.addMultipleToCart');
    Route::delete('/transaksi/hapus/{itemId}', [TransactionController::class, 'removeFromCart'])->name('transactions.removeFromCart');
    Route::post('/transaksi/bayar', [TransactionController::class, 'checkout'])->name('transactions.checkout');
    Route::get('/transaksi/struk', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('/transaksi/download/{id}', [TransactionController::class, 'downloadReceipt'])->name('transactions.downloadReceipt');
    Route::post('/transaksi/reset', [TransactionController::class, 'clearCart'])->name('transactions.clearCart');

    // Stok Item Kasir
    Route::get('/stock', [CashierItemController::class, 'cashierIndex'])->name('stock.index');
    Route::get('/stock/status', [CashierItemController::class, 'getStockStatus'])->name('stock.status');
    Route::post('/stock/warehouse', [CashierItemController::class, 'storeFromWarehouse'])->name('stock.storeFromWarehouse');
    Route::post('/stock/consignment', [CashierItemController::class, 'storeConsignment'])->name('stock.storeConsignment');

    // Barang Titipan (Consignment)
    Route::get('/consignment', [CashierItemController::class, 'consignmentIndex'])->name('consignment.index');
    Route::post('/consignment', [CashierItemController::class, 'storeConsignment'])->name('consignment.store');
    Route::get('/consignment/{cashierItem}/edit', [CashierItemController::class, 'editConsignment'])->name('consignment.edit');
    Route::put('/consignment/{cashierItem}', [CashierItemController::class, 'updateConsignment'])->name('consignment.update');
    Route::delete('/consignment/{cashierItem}', [CashierItemController::class, 'destroyConsignment'])->name('consignment.destroy');

    // History Transaksi POS
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{transaction}', [HistoryController::class, 'show'])->name('history.show');

    // === PESANAN BOOKING ONLINE ===
    Route::get('/bookings', [CashierBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [CashierBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/accept', [CashierBookingController::class, 'accept'])->name('bookings.accept');
    Route::post('/bookings/{booking}/reject', [CashierBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{booking}/process', [CashierBookingController::class, 'process'])->name('bookings.process');
    Route::post('/bookings/{booking}/ready', [CashierBookingController::class, 'ready'])->name('bookings.ready');
    Route::post('/bookings/{booking}/complete', [CashierBookingController::class, 'complete'])->name('bookings.complete');
    // Histori Booking
    Route::get('/booking-history', [CashierBookingController::class, 'history'])->name('bookings.history');
    // API: pending count for badge polling
    Route::get('/api/bookings/pending-count', [CashierBookingController::class, 'pendingCount'])->name('bookings.pendingCount');
});

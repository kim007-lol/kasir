# KasirkuPOS Architecture

KasirkuPOS (repo name: `kasir`) is a web-based point of sale system for retail
stores and small businesses. It combines three surfaces in one Laravel 12
application: the cashier POS, a customer-facing online booking flow, and an
admin back office. This document describes how the codebase is organised, based
on the current `main` branch.

## Stack

- Laravel 12 on PHP 8.2+ (deployed on PHP 8.3), PostgreSQL database
- Laravel UI and Sanctum for authentication, role-based access through the
  `EnsureUserRole` middleware
- Bootstrap 5.3 frontend compiled with Vite, Chart.js for dashboard statistics
- Laravel Reverb with Laravel Echo and Pusher JS for real-time order
  notifications
- BroadcastChannel API (native browser API, no library) to synchronise the
  cashier screen with the customer-facing second display in real time
- barryvdh/laravel-dompdf for PDF reports and receipts, maatwebsite/excel for
  Excel exports
- Gemini integration for the in-app AI assistant (`app/Services/GeminiService.php`),
  store data aggregation in `app/Services/StoreDataService.php`

## Surfaces and their controllers

### Cashier (POS)

- `TransactionController` handles checkout, multi-cart transactions, and
  payment (cash and QRIS)
- `CashierItemController` manages the cashier-side item pool, including
  consignment (titipan) items, discounts, and expiry dates
- `CashierBookingController` processes online bookings that arrive at the till
- `StockAdjustmentController` handles daily stock opname for cashier stock
- The customer display view (`resources/views/cashier/customer-display.blade.php`)
  listens on the `kasirku-customer-display` BroadcastChannel and renders the
  live cart and total for the customer on a second monitor

### Customer (online booking)

- `BookingController` handles catalog browsing, cart, checkout, order status,
  and booking cancellation
- `Auth/CustomerAuthController` handles customer registration and login
- A booking converts into a transaction through `source_booking_id` on the
  transactions table, so online and walk-in sales share one ledger

### Admin (back office)

- `DashboardController` serves sales statistics and best-seller charts
- `WarehouseController` manages warehouse items, stock entries, and transfers
  between warehouse and cashier stock (logged in `stock_transfer_logs`)
- `CategoryController`, `SupplierController`, `MemberController`, and
  `UserController` manage master data and staff accounts
- `ReportController` produces sales, stock movement, and consignment reports
  with PDF and Excel export
- `ShopSettingController` manages shop settings such as opening hours, which
  the booking flow enforces
- `HistoryController` serves transaction history per role

## Core data model

The main Eloquent models in `app/Models`:

- `Transaction` and `TransactionDetail`: the sales ledger, including payment
  columns, member link, cashier name, and purchase price snapshots for margin
  reporting
- `Booking` and `BookingItem`: customer online orders before they convert to
  transactions
- `WarehouseItem`, `CashierItem`, `StockEntry`, `StockAdjustment`,
  `StockTransferLog`: the two-level stock model (warehouse stock and cashier
  stock) with a full movement trail
- `Category`, `Supplier`, `Member`, `ShopSetting`, `User`: master data,
  suppliers support consignment contract dates, users carry a role column

Most master and stock tables use soft deletes, so operational data is
recoverable after accidental deletion.

## Request flow example: online booking to sale

1. A customer books items in `BookingController`; stock and prices are
   re-validated server-side at checkout
2. The booking appears at the till in real time through Reverb notifications
3. The cashier confirms the order in `CashierBookingController`
4. Confirmation creates a `Transaction` linked with `source_booking_id`, stock
   is deducted from `CashierItem`, and the receipt can be printed as a thermal
   receipt or PDF

## Deployment

The app is prepared for Railway: `nixpacks.toml` and the web process binding
are configured for PHP 8.3 with the `pdo_pgsql` and `gd` extensions (the `gd`
extension is required by the Excel export). Local setup is documented in the
project README.

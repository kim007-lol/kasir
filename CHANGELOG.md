# Changelog

All notable changes to KasirkuPOS are documented here. Entries before
2026-11-06 are reconstructed from the Git commit history of this repository
(7 February to 29 March 2026).

## 2026-03-29: Railway deployment and hardening

- Railway deployment configuration: Procfile removed in favour of platform web
  binding, nixpacks config, TrustProxies, PHP pinned to 8.3 with pdo_pgsql, gd,
  and zip extensions for the Excel export
- Security pass: server-side price re-validation at checkout, stock validation,
  unique constraints, environment values moved to config, customer display
  scope tightened
- Bug fixes: discount synchronisation, stock opname redirect, consignment
  (TITIPAN) shortcut, checkout phone number, price precision

## 2026-03-27: Reports

- PDF export added to all reports, rupiah formatting fixed in Excel exports

## 2026-03-24: Validation

- Frontend phone number input validation and expiry date synchronisation

## 2026-03-20: Booking and stock opname

- Customer booking cancellation with UI
- Stock opname enhanced with warehouse and cashier targeting plus history
  reports
- Security and bug audit fixes: race conditions, auth bypass, validation

## 2026-03-04: Booking maturity and branding

- Staff and customer login UI revamp, offline assets, SmegaMart branding
- Auto-cancel logic and alert UI for unpicked booking orders
- Landing page, user validation hardening, secure password generation for
  customer roles, expiry date for cashier stock, stock transfer modal fixes

## 2026-02-27: Booking system overhaul

- Major booking UI overhaul, dynamic layouts and routes across warehouse,
  supplier, member, and user management screens
- Import and export Excel for users, comprehensive user guide generated
- Password and username validation standardised to a minimum of 5 characters
  for all roles

## 2026-02-07 to 2026-02-19: Core build

- Initial Laravel 12 and PostgreSQL build: catalog, transactions, warehouse
  and cashier stock, suppliers, members, consignment, stock transfers,
  bookings, and shop settings (see docs/database.md for the schema timeline)

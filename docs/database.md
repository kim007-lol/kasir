# KasirkuPOS Database Overview

PostgreSQL schema overview for KasirkuPOS, summarised from
`database/migrations`. Migrations are listed in chronological order; the 2024
migrations are the original base schema and the January to February 2026
migrations are the current product schema.

## Sales

- `transactions`: one row per sale. Later migrations add payment columns
  (2026-01-26), `member_id` (2026-02-02), `customer_name`, `cashier_name`
  (2026-02-16), and `source_booking_id` (2026-02-18) linking a sale back to the
  online booking it came from
- `transaction_details`: line items per transaction, with discount stored first
  as a percentage and converted to a rupiah amount (2026-02-13), and a
  `purchase_price` snapshot (2026-02-15) so margin reports stay correct when
  item prices change later

## Catalog and stock

- `categories` and `items`: the base catalog (2024-01-20)
- `suppliers`: includes consignment contract dates (2026-02-09)
- `warehouse_items`: warehouse-level stock with discount precision fixed in
  2026-02-13
- `cashier_items`: cashier-level stock, extended for consignment items
  (2026-02-15), cost price (2026-02-15), and expiry dates (2026-02-19)
- `stock_entries`: incoming stock records
- `stock_transfer_logs`: every warehouse to cashier transfer (2026-02-15)
- Stock adjustments (opname) are recorded through the `StockAdjustment` model

## Booking

- `bookings` and `booking_items` (2026-02-18): customer online orders and their
  line items, converted to transactions when the cashier confirms them

## People and settings

- `users`: Laravel base table extended with `username` (2024-01-24) and `role`
  (2026-02-15) for administrator, cashier, warehouse, and customer access
- `members`: loyalty members (2026-02-02), linked to users in 2026-02-18
- `shop_settings` (2026-02-18): single-row style shop configuration such as
  opening hours enforced by the booking flow

## Conventions

- Soft deletes are enabled on the main operational tables (items, members,
  transactions and stock tables) through migrations in February 2026
- Default users are seeded by the 2026-02-16 migration for first setup
- Money values that affect reports are snapshotted on the transaction detail
  rows instead of being read from the current item price

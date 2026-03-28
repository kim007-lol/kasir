<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add unique constraints to prevent booking_code and invoice collisions.
     */
    public function up(): void
    {
        // Fix duplicate booking_codes if any
        $duplicates = DB::table('bookings')
            ->select('booking_code')
            ->groupBy('booking_code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('booking_code');

        foreach ($duplicates as $code) {
            $rows = DB::table('bookings')->where('booking_code', $code)->orderBy('id')->get();
            foreach ($rows->skip(1)->values() as $i => $row) {
                DB::table('bookings')->where('id', $row->id)->update([
                    'booking_code' => $code . '-DUP' . ($i + 1),
                ]);
            }
        }

        // Fix duplicate invoices if any
        $duplicates = DB::table('transactions')
            ->select('invoice')
            ->groupBy('invoice')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('invoice');

        foreach ($duplicates as $invoice) {
            $rows = DB::table('transactions')->where('invoice', $invoice)->orderBy('id')->get();
            foreach ($rows->skip(1)->values() as $i => $row) {
                DB::table('transactions')->where('id', $row->id)->update([
                    'invoice' => $invoice . '-DUP' . ($i + 1),
                ]);
            }
        }

        // Use CREATE UNIQUE INDEX IF NOT EXISTS for idempotent execution
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS bookings_booking_code_unique ON bookings (booking_code)');
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS transactions_invoice_unique ON transactions (invoice)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS bookings_booking_code_unique');
        DB::statement('DROP INDEX IF EXISTS transactions_invoice_unique');
    }
};

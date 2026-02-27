<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\CashierItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batal otomatis pesanan pickup yang melewati jam ambil awal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $expiredBookings = Booking::where('delivery_type', 'pickup')
            ->whereIn('status', ['pending', 'confirmed', 'processing', 'ready'])
            ->whereNotNull('pickup_time')
            ->where('pickup_time', '<', $now)
            ->get();

        $count = 0;

        foreach ($expiredBookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $lockedBooking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                    if (!$lockedBooking || !in_array($lockedBooking->status, ['pending', 'confirmed', 'processing', 'ready'])) {
                        return; // Skip if already cancelled or completed
                    }

                    // Restore stock ONLY if it was deducted (meaning it passed the 'pending' status)
                    if ($lockedBooking->status !== 'pending') {
                        foreach ($lockedBooking->items as $item) {
                            $cashierItem = CashierItem::find($item->cashier_item_id);
                            if ($cashierItem) {
                                $cashierItem->increment('stock', $item->qty);
                            }
                        }
                    }

                    $lockedBooking->update([
                        'status' => 'cancelled',
                        'cancel_reason' => 'Dibatalkan otomatis oleh sistem (melewati batas waktu ambil)',
                    ]);
                });

                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-cancel booking {$booking->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully auto-cancelled {$count} expired pickup bookings.");
    }
}

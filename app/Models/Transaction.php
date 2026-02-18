<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'customer_name',
        'total',
        'user_id',
        'paid_amount',
        'change_amount',
        'payment_method',
        'member_id',
        'discount_percent',
        'discount_amount',
        'cashier_name',
        'source',
        'booking_id',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get net profit from transaction
     * H3+H4 Fix: Memperhitungkan diskon global (discount_amount)
     */
    public function getNetProfitAttribute(): float
    {
        $grossProfit = $this->details->sum(function ($detail) {
            // Use historical purchase price if available, otherwise fallback to current price
            $purchasePrice = $detail->purchase_price > 0
                ? $detail->purchase_price
                : ($detail->item?->warehouseItem?->purchase_price ?? 0);
            return ($detail->price - $purchasePrice) * $detail->qty;
        });

        // Kurangi diskon global dari profit
        return $grossProfit - ($this->discount_amount ?? 0);
    }
}

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
        'discount_amount'
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

    /**
     * Get net profit from transaction
     */
    public function getNetProfitAttribute(): float
    {
        return $this->details->sum(function ($detail) {
            $purchasePrice = $detail->item?->warehouseItem?->purchase_price ?? 0;
            return ($detail->price - $purchasePrice) * $detail->qty;
        });
    }
}

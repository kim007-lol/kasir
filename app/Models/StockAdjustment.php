<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_item_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'notes',
    ];

    public function cashierItem(): BelongsTo
    {
        return $this->belongsTo(CashierItem::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

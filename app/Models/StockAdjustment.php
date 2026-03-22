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
        'warehouse_item_id',
        'target', // 'cashier' atau 'warehouse'
        // 'user_id' — removed (SEC: must be set from auth()->id() to prevent audit trail forgery)
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

    public function warehouseItem(): BelongsTo
    {
        return $this->belongsTo(WarehouseItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: Get the item name regardless of target type
     */
    public function getItemNameAttribute(): string
    {
        if ($this->target === 'warehouse') {
            return $this->warehouseItem->name ?? '[Dihapus]';
        }
        return $this->cashierItem->name ?? '[Dihapus]';
    }

    /**
     * Helper: Get the item code regardless of target type
     */
    public function getItemCodeAttribute(): string
    {
        if ($this->target === 'warehouse') {
            return $this->warehouseItem->code ?? '-';
        }
        return $this->cashierItem->code ?? '-';
    }
}

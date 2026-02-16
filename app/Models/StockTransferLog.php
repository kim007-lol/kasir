<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferLog extends Model
{
    // L2 Fix: Valid transfer types
    public const VALID_TYPES = [
        'warehouse_to_cashier',
        'cashier_to_warehouse',
        'adjustment',
        'transfer_in',
        'transfer_out',
        'edit_increase',
        'edit_decrease',
        'delete_return',
    ];

    protected static function booted(): void
    {
        static::creating(function (StockTransferLog $log) {
            if (!in_array($log->type, self::VALID_TYPES)) {
                throw new \InvalidArgumentException("Invalid transfer type: {$log->type}. Valid types: " . implode(', ', self::VALID_TYPES));
            }
        });
    }

    protected $fillable = [
        'warehouse_item_id',
        'cashier_item_id',
        'item_name',
        'item_code',
        'quantity',
        'type',
        'notes',
        'user_id',
    ];

    public function warehouseItem()
    {
        return $this->belongsTo(WarehouseItem::class)->withTrashed();
    }

    public function cashierItem()
    {
        return $this->belongsTo(CashierItem::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-readable type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'transfer_in' => 'Transfer ke Kasir',
            'transfer_out' => 'Kembali ke Gudang',
            'edit_increase' => 'Edit Stok (+)',
            'edit_decrease' => 'Edit Stok (-)',
            'delete_return' => 'Hapus Item (Kembali)',
            default => $this->type,
        };
    }

    /**
     * Badge class for display
     */
    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'transfer_in' => 'bg-success',
            'transfer_out' => 'bg-warning text-dark',
            'edit_increase' => 'bg-info',
            'edit_decrease' => 'bg-secondary',
            'delete_return' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}

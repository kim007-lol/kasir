<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_item_id',
        'supplier_id',
        'quantity',
        'entry_date'
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function warehouseItem(): BelongsTo
    {
        return $this->belongsTo(WarehouseItem::class)->withTrashed();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }
}

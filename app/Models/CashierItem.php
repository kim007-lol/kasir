<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_item_id',
        'category_id',
        'supplier_id',
        'code',
        'name',
        'selling_price',
        'discount',
        'stock'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function warehouseItem(): BelongsTo
    {
        return $this->belongsTo(WarehouseItem::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}

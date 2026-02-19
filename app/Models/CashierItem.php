<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

class CashierItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_item_id',
        'category_id',
        'supplier_id',
        'code',
        'name',
        'selling_price',
        'cost_price',
        'discount',
        'stock',
        'expiry_date',
        'is_consignment',
        'consignment_source'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    protected $appends = ['final_price'];

    public function getFinalPriceAttribute()
    {
        return max(0, round($this->selling_price - $this->discount, 2));
    }

    public function warehouseItem(): BelongsTo
    {
        return $this->belongsTo(WarehouseItem::class)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }
}

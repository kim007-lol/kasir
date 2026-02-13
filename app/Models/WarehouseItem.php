<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'code',
        'name',
        'purchase_price',
        'selling_price',
        'discount',
        'stock',
        'exp_date'
    ];

    protected $casts = [
        'exp_date' => 'date',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    protected $appends = ['final_price'];

    public function getFinalPriceAttribute()
    {
        return round($this->selling_price - $this->discount, 2);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cashierItem(): HasOne
    {
        return $this->hasOne(CashierItem::class);
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }
}

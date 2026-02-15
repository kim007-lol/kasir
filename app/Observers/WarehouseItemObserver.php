<?php

namespace App\Observers;

use App\Models\WarehouseItem;
use App\Models\CashierItem;

class WarehouseItemObserver
{
    /**
     * Handle the WarehouseItem "created" event.
     */
    public function created(WarehouseItem $warehouseItem): void
    {
        $this->syncToCashier($warehouseItem);
    }

    /**
     * Handle the WarehouseItem "updated" event.
     */
    public function updated(WarehouseItem $warehouseItem): void
    {
        $this->syncToCashier($warehouseItem);
    }

    /**
     * Handle the WarehouseItem "deleted" event.
     */
    public function deleted(WarehouseItem $warehouseItem): void
    {
        CashierItem::where('warehouse_item_id', $warehouseItem->id)->delete();
    }

    /**
     * Sync WarehouseItem data to CashierItem.
     */
    /**
     * Sync WarehouseItem metadata (name, price, etc.) to CashierItem if it exists.
     * Do NOT sync stock automatically.
     */
    private function syncToCashier(WarehouseItem $warehouseItem): void
    {
        // Only update metadata if the item already exists in Cashier
        $cashierItem = CashierItem::where('warehouse_item_id', $warehouseItem->id)->first();

        if ($cashierItem) {
            $cashierItem->update([
                'code' => $warehouseItem->code,
                'name' => $warehouseItem->name,
                'selling_price' => $warehouseItem->final_price,
                // 'stock' => $warehouseItem->stock, // DO NOT SYNC STOCK
                'discount' => $warehouseItem->discount,
                'category_id' => $warehouseItem->category_id,
                'supplier_id' => $warehouseItem->supplier_id
            ]);
        }
    }
}

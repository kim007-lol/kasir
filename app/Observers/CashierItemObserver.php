<?php

namespace App\Observers;

use App\Models\CashierItem;
use App\Models\WarehouseItem;

class CashierItemObserver
{
    /**
     * Handle the CashierItem "updated" event.
     */
    public function updated(CashierItem $cashierItem): void
    {
        // Logic removed: Stock changes in CashierItem should NOT affect WarehouseItem stock automatically.
        // Transfers handle the initial movement. Sales only affect Cashier stock.
    }
}

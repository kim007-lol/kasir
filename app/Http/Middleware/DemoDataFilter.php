<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\WarehouseItem;
use App\Models\CashierItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Member;
use App\Models\StockEntry;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\StockAdjustment;
use App\Models\StockTransferLog;
use App\Models\ShopSetting;

class DemoDataFilter
{
    /**
     * Ketika demo user login, semua query di-scope agar
     * HANYA menampilkan data demo — data real 100% tersembunyi.
     *
     * SECURITY: Setiap model yang punya data sensitif HARUS di-scope di sini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (isDemoUser()) {
            $this->applyDemoScopes();
        }

        return $next($request);
    }

    private function applyDemoScopes(): void
    {
        // Demo user IDs — ambil TANPA global scope agar tidak loop
        $demoUserIds = User::withoutGlobalScopes()
            ->where('username', 'like', 'demo_%')
            ->pluck('id')
            ->toArray();

        // ============================================================
        // 1. USER — hanya tampilkan akun demo_*
        // ============================================================
        User::addGlobalScope('demo_only', function ($query) {
            $query->where('username', 'like', 'demo_%');
        });

        // ============================================================
        // 2. WAREHOUSE ITEMS — hanya kode DW-*
        // ============================================================
        WarehouseItem::addGlobalScope('demo_only', function ($query) {
            $query->where('code', 'like', 'DW-%');
        });

        // ============================================================
        // 3. CASHIER ITEMS — hanya kode DC-* atau DCONS-*
        // ============================================================
        CashierItem::addGlobalScope('demo_only', function ($query) {
            $query->where(function ($q) {
                $q->where('code', 'like', 'DC-%')
                  ->orWhere('code', 'like', 'DCONS-%');
            });
        });

        // ============================================================
        // 4. TRANSACTION — hanya invoice DINV-*
        // ============================================================
        Transaction::addGlobalScope('demo_only', function ($query) {
            $query->where('invoice', 'like', 'DINV-%');
        });

        // ============================================================
        // 5. TRANSACTION DETAIL — hanya milik transaksi demo
        // ============================================================
        TransactionDetail::addGlobalScope('demo_only', function ($query) {
            $query->whereHas('transaction', function ($q) {
                $q->withoutGlobalScopes()->where('invoice', 'like', 'DINV-%');
            });
        });

        // ============================================================
        // 6. BOOKING — hanya milik demo users
        // ============================================================
        Booking::addGlobalScope('demo_only', function ($query) use ($demoUserIds) {
            $query->whereIn('user_id', $demoUserIds);
        });

        // ============================================================
        // 7. BOOKING ITEM — hanya milik booking demo
        // ============================================================
        BookingItem::addGlobalScope('demo_only', function ($query) use ($demoUserIds) {
            $query->whereHas('booking', function ($q) use ($demoUserIds) {
                $q->withoutGlobalScopes()->whereIn('user_id', $demoUserIds);
            });
        });

        // ============================================================
        // 8. MEMBER — HANYA member yang terkait demo user
        //    FIX #2: Menghapus orWhereNull('user_id') yang mem-bocorkan
        //    member real tanpa akun login
        // ============================================================
        Member::addGlobalScope('demo_only', function ($query) use ($demoUserIds) {
            $query->whereIn('user_id', $demoUserIds);
        });

        // ============================================================
        // 9. STOCK ENTRY — hanya milik warehouse item demo
        // ============================================================
        StockEntry::addGlobalScope('demo_only', function ($query) {
            $query->whereHas('warehouseItem', function ($q) {
                $q->withoutGlobalScopes()->where('code', 'like', 'DW-%');
            });
        });

        // ============================================================
        // 10. STOCK ADJUSTMENT — sembunyikan semua (tidak ada demo data)
        // ============================================================
        StockAdjustment::addGlobalScope('demo_only', function ($query) {
            $query->whereRaw('1 = 0');
        });

        // ============================================================
        // 11. STOCK TRANSFER LOG — FIX #4: sembunyikan log transfer real
        // ============================================================
        StockTransferLog::addGlobalScope('demo_only', function ($query) use ($demoUserIds) {
            $query->where(function ($q) use ($demoUserIds) {
                // Hanya tampilkan log milik demo users atau dengan item demo
                $q->whereIn('user_id', $demoUserIds)
                  ->orWhere(function ($sub) {
                      $sub->whereHas('warehouseItem', function ($wi) {
                          $wi->withoutGlobalScopes()->where('code', 'like', 'DW-%');
                      });
                  })
                  ->orWhere(function ($sub) {
                      $sub->whereHas('cashierItem', function ($ci) {
                          $ci->withoutGlobalScopes()->where(function ($q2) {
                              $q2->where('code', 'like', 'DC-%')
                                 ->orWhere('code', 'like', 'DCONS-%');
                          });
                      });
                  });
            });
        });

        // ============================================================
        // 12. SUPPLIER — FIX #5: hanya supplier yang terkait item demo
        // ============================================================
        Supplier::addGlobalScope('demo_only', function ($query) {
            $query->whereHas('warehouseItems', function ($q) {
                $q->withoutGlobalScopes()->where('code', 'like', 'DW-%');
            });
        });

        // ============================================================
        // 13. CATEGORY — FIX #5: hanya kategori yang terkait item demo
        // ============================================================
        Category::addGlobalScope('demo_only', function ($query) {
            $query->whereHas('items', function ($q) {
                $q->withoutGlobalScopes()->where('code', 'like', 'DW-%');
            });
        });

        // ============================================================
        // 14. SHOP SETTING — FIX #6: sembunyikan setting real
        // ============================================================
        ShopSetting::addGlobalScope('demo_only', function ($query) {
            $query->whereRaw('1 = 0');
        });
    }
}

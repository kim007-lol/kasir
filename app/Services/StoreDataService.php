<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\CashierItem;
use App\Models\WarehouseItem;
use App\Models\Member;
use App\Models\Category;
use App\Models\Booking;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreDataService
{
    /**
     * Gather a comprehensive context of store data for the AI.
     */
    public function getContext(): array
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek  = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $lastWeek  = [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()];
        $thisMonth = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        $lastMonth = [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];

        return [
            'tanggal_hari_ini'  => $today->format('d M Y (l)'),
            'ringkasan_hari_ini' => $this->getSalesSummary($today, $today),
            'ringkasan_kemarin' => $this->getSalesSummary($yesterday, $yesterday),
            'ringkasan_minggu_ini' => $this->getSalesSummary($thisWeek[0], $thisWeek[1]),
            'ringkasan_minggu_lalu' => $this->getSalesSummary($lastWeek[0], $lastWeek[1]),
            'ringkasan_bulan_ini' => $this->getSalesSummary($thisMonth[0], $thisMonth[1]),
            'ringkasan_bulan_lalu' => $this->getSalesSummary($lastMonth[0], $lastMonth[1]),
            'histori_bulanan_12_bulan_terakhir' => $this->getMonthlyHistory(12),
            'top_5_produk_terlaris_bulan_ini' => $this->getTopProducts($thisMonth[0], $thisMonth[1]),
            'kategori_penjualan_bulan_ini' => $this->getCategorySales($thisMonth[0], $thisMonth[1]),
            'stok_habis_di_kasir' => $this->getZeroStockItems(),
            'stok_rendah_di_kasir' => $this->getLowStockItems(10),
            'daftar_sebagian_stok_gudang' => $this->getWarehouseItems(),
            'total_member' => Member::count(),
            'total_supplier' => Supplier::count(),
            'total_produk_di_kasir' => CashierItem::count(),
            'total_produk_di_gudang' => WarehouseItem::count(),
            'booking_menunggu' => Booking::where('status', 'pending')->count(),
            'daftar_kategori' => $this->getAllCategories(),
            'daftar_supplier' => $this->getAllSuppliers(),
            'menu_sidebar_aplikasi' => $this->getSidebarMenu(),
        ];
    }

    protected function getSalesSummary(Carbon $from, Carbon $to): array
    {
        $transactions = Transaction::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);

        $totalTransactions = (clone $transactions)->count();
        $totalRevenue      = (clone $transactions)->sum('total');
        $totalDiscount     = (clone $transactions)->sum('discount_amount');

        $transactionIds = (clone $transactions)->pluck('id');
        $totalItemsSold = TransactionDetail::whereIn('transaction_id', $transactionIds)->sum('qty');

        // Calculate COGS
        $totalCost = TransactionDetail::whereIn('transaction_id', $transactionIds)
            ->selectRaw('SUM(qty * CASE WHEN purchase_price > 0 THEN purchase_price ELSE 0 END) as total')
            ->value('total') ?? 0;

        $netProfit = $totalRevenue - $totalCost - $totalDiscount;

        // Source breakdown
        $posCount    = (clone $transactions)->where(function ($q) {
            $q->where('source', 'pos')->orWhereNull('source');
        })->count();
        $onlineCount = (clone $transactions)->where('source', 'online')->count();

        return [
            'jumlah_transaksi' => $totalTransactions,
            'total_pendapatan' => round($totalRevenue),
            'total_diskon'     => round($totalDiscount),
            'laba_bersih'      => round($netProfit),
            'total_barang_terjual' => $totalItemsSold,
            'transaksi_pos'    => $posCount,
            'transaksi_online' => $onlineCount,
        ];
    }

    protected function getMonthlyHistory(int $months = 12): array
    {
        $history = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonthsNoOverflow($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $monthName = $date->format('F Y'); // e.g., "August 2025"
            $summary = $this->getSalesSummary($startOfMonth, $endOfMonth);
            
            $history[$monthName] = [
                'total_pendapatan' => $summary['total_pendapatan'],
                'laba_bersih' => $summary['laba_bersih'],
                'jumlah_transaksi' => $summary['jumlah_transaksi']
            ];
        }
        
        return $history;
    }

    protected function getTopProducts(Carbon $from, Carbon $to, int $limit = 5): array
    {
        $transactionIds = Transaction::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->pluck('id');

        return TransactionDetail::select('item_id', DB::raw('SUM(qty) as total_qty'))
            ->whereIn('transaction_id', $transactionIds)
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->get()
            ->map(fn($d) => [
                'nama'        => $d->item?->name ?? '[Dihapus]',
                'terjual'     => $d->total_qty,
                'harga_jual'  => $d->item?->final_price ?? 0,
            ])
            ->toArray();
    }

    protected function getCategorySales(Carbon $from, Carbon $to): array
    {
        $transactionIds = Transaction::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->pluck('id');

        return TransactionDetail::select('item_id', DB::raw('SUM(qty) as total_qty'))
            ->whereIn('transaction_id', $transactionIds)
            ->with('item.category')
            ->groupBy('item_id')
            ->get()
            ->groupBy(fn($d) => $d->item?->category?->name ?? 'Tanpa Kategori')
            ->map(fn($items, $category) => [
                'kategori'    => $category,
                'total_terjual' => $items->sum('total_qty'),
            ])
            ->values()
            ->sortByDesc('total_terjual')
            ->values()
            ->toArray();
    }

    protected function getLowStockItems(int $threshold = 10, int $limit = 20): array
    {
        return CashierItem::where('stock', '>', 0)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->take($limit)
            ->get(['name', 'stock', 'code'])
            ->map(fn($item) => [
                'kode'  => $item->code,
                'nama'  => $item->name,
                'stok'  => $item->stock,
            ])
            ->toArray();
    }

    protected function getZeroStockItems(int $limit = 20): array
    {
        return CashierItem::where('stock', '<=', 0)
            ->take($limit)
            ->get(['name', 'stock', 'code'])
            ->map(fn($item) => [
                'kode'  => $item->code,
                'nama'  => $item->name,
                'stok'  => 0,
            ])
            ->toArray();
    }

    protected function getWarehouseItems(int $limit = 20): array
    {
        return WarehouseItem::orderByDesc('stock')
            ->take($limit)
            ->get(['name', 'stock', 'code'])
            ->map(fn($item) => [
                'kode'  => $item->code,
                'nama'  => $item->name,
                'stok_gudang'  => $item->stock,
            ])
            ->toArray();
    }

    protected function getAllCategories(): array
    {
        return Category::get(['name'])->pluck('name')->toArray();
    }

    protected function getAllSuppliers(): array
    {
        return Supplier::get(['name'])->pluck('name')->toArray();
    }

    protected function getSidebarMenu(): array
    {
        return [
            ['nama' => 'Dashboard', 'deskripsi' => 'Ringkasan performa toko, grafik penjualan, dan statistik cepat.'],
            ['nama' => 'Gudang', 'deskripsi' => 'Manajemen stok barang mentah atau stok besar di gudang.'],
            ['nama' => 'Supplier', 'deskripsi' => 'Daftar pemasok barang untuk toko Anda.'],
            ['nama' => 'Kategori', 'deskripsi' => 'Pengelompokan barang (misal: Makanan, Minuman, Elektronik).'],
            ['nama' => 'Stok Item Kasir', 'deskripsi' => 'Daftar barang yang siap dijual di kasir/POS.'],
            ['nama' => 'Stock Opname', 'deskripsi' => 'Pencocokan stok fisik dengan stok di sistem.'],
            ['nama' => 'Histori Transaksi', 'deskripsi' => 'Daftar semua penjualan yang pernah terjadi.'],
            ['nama' => 'Member', 'deskripsi' => 'Manajemen data pelanggan tetap atau member toko.'],
            ['nama' => 'Laporan', 'deskripsi' => 'Laporan penjualan detail, laba rugi, dan PDF laporan.'],
            ['nama' => 'Kelola User', 'deskripsi' => 'Pengaturan staf, kasir, dan hak akses aplikasi.'],
            ['nama' => 'Tanya Toko AI', 'deskripsi' => 'Asisten chatbot (halaman ini) untuk tanya jawab data bisnis.'],
        ];
    }
}

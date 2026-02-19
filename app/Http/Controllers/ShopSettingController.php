<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use Illuminate\Http\Request;

class ShopSettingController extends Controller
{
    /**
     * Show the shop settings form (rendered on the cashier dashboard).
     */
    public function edit()
    {
        $openHour = ShopSetting::get('open_hour', '07:00');
        $closeHour = ShopSetting::get('close_hour', '15:00');
        $override = ShopSetting::get('shop_open_override');
        $isOpen = ShopSetting::isShopOpen();

        return view('cashier.shop-settings', compact('openHour', 'closeHour', 'override', 'isOpen'));
    }

    /**
     * Update shop hours.
     */
    public function update(Request $request)
    {
        $request->validate([
            'open_hour' => 'required|date_format:H:i',
            'close_hour' => 'required|date_format:H:i|after:open_hour',
        ], [
            'open_hour.required' => 'Jam buka harus diisi',
            'close_hour.required' => 'Jam tutup harus diisi',
            'close_hour.after' => 'Jam tutup harus lebih besar dari jam buka',
        ]);

        ShopSetting::set('open_hour', $request->open_hour);
        ShopSetting::set('close_hour', $request->close_hour);

        return back()->with('success', 'Jam operasional berhasil diperbarui!');
    }

    /**
     * Toggle shop open/closed manually.
     */
    public function toggleOverride(Request $request)
    {
        $request->validate([
            'override' => 'required|in:open,closed,auto',
        ]);

        $value = $request->override === 'auto' ? null : $request->override;
        ShopSetting::set('shop_open_override', $value);

        $label = match ($request->override) {
            'open' => 'Toko dipaksa BUKA',
            'closed' => 'Toko dipaksa TUTUP',
            'auto' => 'Toko kembali ke jadwal otomatis',
        };

        return back()->with('success', $label);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ShopSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("shop_setting_{$key}", 60, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        Cache::forget("shop_setting_{$key}");
    }

    /**
     * Check if shop is currently open based on settings.
     */
    public static function isShopOpen(): bool
    {
        // Manual override: 'open' = force open, 'closed' = force closed, null = auto
        $override = static::get('shop_open_override');
        if ($override === 'open') return true;
        if ($override === 'closed') return false;

        $now = now();

        // Closed on Sundays
        if ($now->dayOfWeek === 0) {
            return false;
        }

        $openHour = static::get('open_hour', '07:00');
        $closeHour = static::get('close_hour', '15:00');

        $currentTime = $now->format('H:i');
        return $currentTime >= $openHour && $currentTime < $closeHour;
    }
}

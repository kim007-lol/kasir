<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Behavior:
     * - Default users (admin, kasir, pelanggan) are ALWAYS created
     *   via migration: 2026_02_16_010600_seed_default_users.php
     *
     * - If APP_MODE=demo → also run DemoSeeder to generate dummy data
     *   (demo accounts, 60 warehouse items, 50 cashier items, 30 members,
     *    60 transactions, 30 bookings)
     *
     * Usage:
     *   php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        if (config('app.mode') === 'demo') {
            $this->command->info('🎭 APP_MODE=demo terdeteksi — menjalankan DemoSeeder...');
            $this->call(DemoSeeder::class);
        } else {
            $this->command->info('🏪 Mode produksi — hanya default users (dari migration).');
        }
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HistoryRawDbTest extends TestCase
{
    public function test_raw_insert()
    {
        echo "Starting raw insert test...\n";
        try {
            $id = DB::table('users')->insertGetId([
                'name' => 'Raw Test',
                'username' => 'rawtest_' . uniqid(),
                'email' => 'raw_' . uniqid() . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Inserted ID: $id\n";
            $this->assertTrue(true);

            DB::table('users')->where('id', $id)->delete();
        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

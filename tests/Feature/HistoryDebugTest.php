<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class HistoryDebugTest extends TestCase
{
    public function test_simple_execution()
    {
        Log::info('Test starting');
        echo "Starting test...\n";

        $user = null;
        try {
            $user = User::factory()->create(['email' => 'debug_' . uniqid() . '@test.com']);
            echo "User factory create works. ID: " . $user->id . "\n";
            $this->assertTrue(true);
        } catch (\Throwable $e) {
            echo "Error: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            if ($user && $user->exists) {
                $user->delete();
                echo "User deleted.\n";
            }
        }
    }
}

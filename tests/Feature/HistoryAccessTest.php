<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HistoryAccessTest extends TestCase
{
    // use DatabaseTransactions;

    protected function tearDown(): void
    {
        // Manual cleanup
        if (isset($this->app)) {
            Transaction::where('invoice', 'LIKE', 'TEST-%')->delete();
            User::where('email', 'LIKE', '%@test.com')->delete();
        }
        parent::tearDown();
    }

    public function test_admin_can_see_all_transactions()
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_' . microtime(true) . '@test.com']);
        $cashier1 = User::factory()->create(['role' => 'kasir', 'email' => 'c1_' . microtime(true) . '@test.com']);
        $cashier2 = User::factory()->create(['role' => 'kasir', 'email' => 'c2_' . microtime(true) . '@test.com']);

        Transaction::factory()->create(['user_id' => $cashier1->id, 'invoice' => 'TEST-' . uniqid()]);
        Transaction::factory()->create(['user_id' => $cashier2->id, 'invoice' => 'TEST-' . uniqid()]);

        $response = $this->actingAs($admin)->get(route('history.index'));

        $response->assertStatus(200);
        $response->assertViewHas('transactions', function ($transactions) {
            return $transactions->count() === 2;
        });
    }

    public function test_cashier_can_only_see_their_own_transactions()
    {
        $cashier1 = User::factory()->create(['role' => 'kasir', 'email' => 'c1b_' . microtime(true) . '@test.com']);
        $cashier2 = User::factory()->create(['role' => 'kasir', 'email' => 'c2b_' . microtime(true) . '@test.com']);

        $transaction1 = Transaction::factory()->create(['user_id' => $cashier1->id, 'invoice' => 'TEST-' . uniqid()]);
        $transaction2 = Transaction::factory()->create(['user_id' => $cashier2->id, 'invoice' => 'TEST-' . uniqid()]);

        // Cashier 1 viewing history
        $response = $this->actingAs($cashier1)->get(route('history.index'));

        $response->assertStatus(200);
        $response->assertViewHas('transactions', function ($transactions) use ($transaction1, $transaction2) {
            $ids = $transactions->pluck('id');
            dd('Visible IDs:', $ids->toArray(), 'Expected:', $transaction1->id, 'Should NOT see:', $transaction2->id);

            if (!$ids->contains($transaction1->id)) {
                dump("FAILURE: Expected transaction {$transaction1->id} is missing.");
            }
            if ($ids->contains($transaction2->id)) {
                dump("FAILURE: Unexpected transaction {$transaction2->id} is present.");
            }

            return $ids->contains($transaction1->id) && !$ids->contains($transaction2->id);
        });
    }
}

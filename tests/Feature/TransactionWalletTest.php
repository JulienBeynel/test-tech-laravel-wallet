<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;

test('it shows dashboard with transactions and balance', function() {
    // Create an user with a wallet and transactions
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create(['balance' => 5000]);
    $transactions = WalletTransaction::factory(3)->for($wallet)->create();

    // We simulate request with the connected user
    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard');
    $response->assertViewHas('balance', 5000);
    $response->assertViewHas('transactions');

    expect($response->viewData('transactions')->count())->toBe(3);

    foreach($transactions as $transaction){
        $response->assertSee((string) $transaction->id);
    }
});

test('it handles users without wallet', function() {
    // Create an user without wallet
    $user = User::factory()->create();

    // We simulate request with the connected user
    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard');
    $response->assertViewHas('balance', 0);

    expect($response->viewData('transactions'))->toBeEmpty();
});

test('it handles users with wallet but no transactions', function() {
    // Create an user with empty wallet
    $user = User::factory()->create();
    Wallet::factory()->for($user)->create(['balance' => 0]);

    // We simulate request with the connected user
    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard');
    $response->assertViewHas('balance', 0);

    $transactionsData = $response->viewData('transactions');

    expect(is_countable($transactionsData) ? count($transactionsData) : 0)->toBe(0);
});
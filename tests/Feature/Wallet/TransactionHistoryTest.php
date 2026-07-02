<?php

use App\Livewire\Wallet\TransactionHistory;
use App\Models\User;
use App\Services\WalletService;
use Livewire\Livewire;

it('lista as transações do usuário', function () {
    $alice = User::factory()->withBalanceCents(0)->create();
    app(WalletService::class)->deposit($alice->wallet, 5000);

    Livewire::actingAs($alice)
        ->test(TransactionHistory::class)
        ->assertOk()
        ->assertSee('Depósito');
});

it('permite ao iniciador estornar pelo extrato', function () {
    $alice = User::factory()->withBalanceCents(0)->create();
    $deposit = app(WalletService::class)->deposit($alice->wallet, 5000);

    Livewire::actingAs($alice)
        ->test(TransactionHistory::class)
        ->call('reverse', $deposit->uuid)
        ->assertDispatched('wallet-updated');

    expect($alice->wallet->refresh()->balance_cents)->toBe(0)
        ->and($deposit->refresh()->isReversed())->toBeTrue();
});

it('impede o estorno por quem não é o iniciador', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();
    $transfer = app(WalletService::class)->transfer($alice->wallet, $bob->wallet, 1000);

    Livewire::actingAs($bob)
        ->test(TransactionHistory::class)
        ->call('reverse', $transfer->uuid)
        ->assertForbidden();

    // Saldos permanecem inalterados
    expect($alice->wallet->refresh()->balance_cents)->toBe(9000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(1000);
});

<?php

use App\Models\User;
use App\Services\WalletService;

beforeEach(function () {
    $this->service = app(WalletService::class);
});

it('reporta consistência quando os saldos batem com o ledger', function () {
    $alice = User::factory()->create(); // carteira zerada (via Observer)
    $bob = User::factory()->create();

    // Movimenta tudo pelo WalletService: ledger e saldos ficam em sincronia.
    $this->service->deposit($alice->wallet, 10000);
    $tx = $this->service->transfer($alice->wallet->refresh(), $bob->wallet, 4000);
    $this->service->reverse($tx);

    $this->artisan('wallet:reconcile')
        ->expectsOutputToContain('consistentes com o ledger')
        ->assertSuccessful();
});

it('detecta carteira com saldo divergente do ledger', function () {
    $alice = User::factory()->create();
    $this->service->deposit($alice->wallet, 10000);

    // Corrupção simulada: escrita direta no saldo, fora do WalletService.
    $alice->wallet->refresh()->update(['balance_cents' => 99999]);

    $this->artisan('wallet:reconcile')
        ->expectsOutputToContain('divergente')
        ->assertFailed();
});

it('considera divergente a carteira com saldo sem lastro no ledger', function () {
    // withBalanceCents grava direto no banco, sem lançamento correspondente.
    User::factory()->withBalanceCents(5000)->create();

    $this->artisan('wallet:reconcile')->assertFailed();
});

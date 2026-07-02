<?php

use App\Enums\TransactionType;
use App\Exceptions\IdempotencyConflictException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\SelfTransferException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;

beforeEach(function () {
    $this->service = app(WalletService::class);
});

it('transfere fundos entre carteiras', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();

    $tx = $this->service->transfer($alice->wallet, $bob->wallet, 4000);

    expect($alice->wallet->refresh()->balance_cents)->toBe(6000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(4000)
        ->and($tx->type)->toBe(TransactionType::Transfer)
        ->and($tx->from_wallet_id)->toBe($alice->wallet->id)
        ->and($tx->to_wallet_id)->toBe($bob->wallet->id);
});

it('bloqueia transferência sem saldo suficiente e não altera saldos', function () {
    $alice = User::factory()->withBalanceCents(3000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();

    expect(fn () => $this->service->transfer($alice->wallet, $bob->wallet, 5000))
        ->toThrow(InsufficientBalanceException::class);

    expect($alice->wallet->refresh()->balance_cents)->toBe(3000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(0)
        ->and(Transaction::count())->toBe(0);
});

it('não permite transferir para a própria carteira', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();

    expect(fn () => $this->service->transfer($alice->wallet, $alice->wallet, 1000))
        ->toThrow(SelfTransferException::class);
});

it('é idempotente por chave', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();

    $a = $this->service->transfer($alice->wallet, $bob->wallet, 2000, 'tkey');
    $b = $this->service->transfer($alice->wallet, $bob->wallet, 2000, 'tkey');

    expect($a->id)->toBe($b->id)
        ->and($alice->wallet->refresh()->balance_cents)->toBe(8000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(2000)
        ->and(Transaction::count())->toBe(1);
});

it('rejeita reuso da chave de idempotência com parâmetros diferentes', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();
    $carol = User::factory()->withBalanceCents(0)->create();

    $this->service->transfer($alice->wallet, $bob->wallet, 2000, 'tkey');

    // Mesma chave, valor diferente.
    expect(fn () => $this->service->transfer($alice->wallet, $bob->wallet, 5000, 'tkey'))
        ->toThrow(IdempotencyConflictException::class);

    // Mesma chave, destinatário diferente.
    expect(fn () => $this->service->transfer($alice->wallet, $carol->wallet, 2000, 'tkey'))
        ->toThrow(IdempotencyConflictException::class);

    expect($alice->wallet->refresh()->balance_cents)->toBe(8000)
        ->and(Transaction::count())->toBe(1);
});

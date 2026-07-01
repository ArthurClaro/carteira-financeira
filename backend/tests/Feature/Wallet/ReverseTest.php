<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\CannotReverseReversalException;
use App\Exceptions\TransactionAlreadyReversedException;
use App\Models\User;
use App\Services\WalletService;

beforeEach(function () {
    $this->service = app(WalletService::class);
});

it('estorna um depósito debitando a carteira', function () {
    $user = User::factory()->withBalanceCents(0)->create();
    $deposit = $this->service->deposit($user->wallet, 5000);

    $reversal = $this->service->reverse($deposit);

    expect($user->wallet->refresh()->balance_cents)->toBe(0)
        ->and($reversal->type)->toBe(TransactionType::Reversal)
        ->and($reversal->related_transaction_id)->toBe($deposit->id)
        ->and($deposit->refresh()->status)->toBe(TransactionStatus::Reversed);
});

it('estorna uma transferência restaurando os saldos', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();
    $transfer = $this->service->transfer($alice->wallet, $bob->wallet, 4000);

    $this->service->reverse($transfer);

    expect($alice->wallet->refresh()->balance_cents)->toBe(10000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(0)
        ->and($transfer->refresh()->status)->toBe(TransactionStatus::Reversed);
});

it('não permite estornar duas vezes', function () {
    $user = User::factory()->withBalanceCents(0)->create();
    $deposit = $this->service->deposit($user->wallet, 5000);
    $this->service->reverse($deposit);

    expect(fn () => $this->service->reverse($deposit->refresh()))
        ->toThrow(TransactionAlreadyReversedException::class);
});

it('não permite estornar um estorno', function () {
    $user = User::factory()->withBalanceCents(0)->create();
    $deposit = $this->service->deposit($user->wallet, 5000);
    $reversal = $this->service->reverse($deposit);

    expect(fn () => $this->service->reverse($reversal))
        ->toThrow(CannotReverseReversalException::class);
});

it('estorno pode deixar o saldo negativo', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();
    $charlie = User::factory()->withBalanceCents(0)->create();

    $transfer = $this->service->transfer($alice->wallet, $bob->wallet, 4000);
    // Bob gasta tudo antes do estorno
    $this->service->transfer($bob->wallet, $charlie->wallet, 4000);

    $this->service->reverse($transfer);

    expect($bob->wallet->refresh()->balance_cents)->toBe(-4000)
        ->and($alice->wallet->refresh()->balance_cents)->toBe(10000);
});

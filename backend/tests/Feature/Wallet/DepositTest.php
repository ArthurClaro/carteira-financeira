<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InvalidAmountException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;

beforeEach(function () {
    $this->service = app(WalletService::class);
});

it('deposita e aumenta o saldo', function () {
    $user = User::factory()->withBalanceCents(0)->create();

    $tx = $this->service->deposit($user->wallet, 15000);

    expect($user->wallet->refresh()->balance_cents)->toBe(15000)
        ->and($tx->type)->toBe(TransactionType::Deposit)
        ->and($tx->status)->toBe(TransactionStatus::Completed)
        ->and($tx->to_wallet_id)->toBe($user->wallet->id)
        ->and($tx->from_wallet_id)->toBeNull();
});

it('depósito em saldo negativo acrescenta ao valor', function () {
    $user = User::factory()->withBalanceCents(-5000)->create();

    $this->service->deposit($user->wallet, 3000);

    // -50,00 + 30,00 = -20,00
    expect($user->wallet->refresh()->balance_cents)->toBe(-2000);
});

it('rejeita depósito de valor não positivo', function () {
    $user = User::factory()->withBalanceCents(0)->create();

    expect(fn () => $this->service->deposit($user->wallet, 0))
        ->toThrow(InvalidAmountException::class);
});

it('não duplica depósito com a mesma chave de idempotência', function () {
    $user = User::factory()->withBalanceCents(0)->create();

    $a = $this->service->deposit($user->wallet, 1000, 'key-1');
    $b = $this->service->deposit($user->wallet, 1000, 'key-1');

    expect($a->id)->toBe($b->id)
        ->and($user->wallet->refresh()->balance_cents)->toBe(1000)
        ->and(Transaction::count())->toBe(1);
});

<?php

use App\Models\User;
use App\Services\WalletService;

it('permite o remetente estornar a própria transferência', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();
    $transfer = app(WalletService::class)->transfer($alice->wallet, $bob->wallet, 1000);

    expect($alice->can('reverse', $transfer))->toBeTrue();
});

it('impede o destinatário de estornar transferência alheia', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();
    $transfer = app(WalletService::class)->transfer($alice->wallet, $bob->wallet, 1000);

    expect($bob->can('reverse', $transfer))->toBeFalse();
});

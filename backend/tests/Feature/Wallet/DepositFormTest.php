<?php

use App\Livewire\Wallet\DepositForm;
use App\Models\User;
use Livewire\Livewire;

it('realiza depósito e emite evento wallet-updated', function () {
    $user = User::factory()->withBalanceCents(0)->create();

    Livewire::actingAs($user)
        ->test(DepositForm::class)
        ->set('amount', '100.50')
        ->call('deposit')
        ->assertHasNoErrors()
        ->assertDispatched('wallet-updated');

    expect($user->wallet->refresh()->balance_cents)->toBe(10050);
});

it('valida valor obrigatório e positivo', function () {
    $user = User::factory()->withBalanceCents(0)->create();

    Livewire::actingAs($user)
        ->test(DepositForm::class)
        ->set('amount', '0')
        ->call('deposit')
        ->assertHasErrors('amount');

    expect($user->wallet->refresh()->balance_cents)->toBe(0);
});

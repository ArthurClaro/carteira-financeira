<?php

use App\Livewire\Wallet\TransferForm;
use App\Models\User;
use Livewire\Livewire;

it('transfere para outro usuário', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();

    Livewire::actingAs($alice)
        ->test(TransferForm::class)
        ->set('recipient_email', $bob->email)
        ->set('amount', '40.00')
        ->call('transfer')
        ->assertHasNoErrors()
        ->assertDispatched('wallet-updated');

    expect($alice->wallet->refresh()->balance_cents)->toBe(6000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(4000);
});

it('erro quando o destinatário não existe', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();

    Livewire::actingAs($alice)
        ->test(TransferForm::class)
        ->set('recipient_email', 'ninguem@example.com')
        ->set('amount', '10.00')
        ->call('transfer')
        ->assertHasErrors('recipient_email');
});

it('erro ao transferir para si mesmo', function () {
    $alice = User::factory()->withBalanceCents(10000)->create();

    Livewire::actingAs($alice)
        ->test(TransferForm::class)
        ->set('recipient_email', $alice->email)
        ->set('amount', '10.00')
        ->call('transfer')
        ->assertHasErrors('recipient_email');
});

it('erro de saldo insuficiente não altera saldos', function () {
    $alice = User::factory()->withBalanceCents(1000)->create();
    $bob = User::factory()->withBalanceCents(0)->create();

    Livewire::actingAs($alice)
        ->test(TransferForm::class)
        ->set('recipient_email', $bob->email)
        ->set('amount', '50.00')
        ->call('transfer')
        ->assertHasErrors('amount');

    expect($alice->wallet->refresh()->balance_cents)->toBe(1000)
        ->and($bob->wallet->refresh()->balance_cents)->toBe(0);
});

it('aplica rate limiting após exceder o limite de transferências', function () {
    config(['wallet.throttle.max_attempts' => 3]);

    $sender = User::factory()->withBalanceCents(100000)->create();
    $receiver = User::factory()->withBalanceCents(0)->create();
    $component = Livewire::actingAs($sender)->test(TransferForm::class);

    for ($i = 0; $i < 3; $i++) {
        $component->set('recipient_email', $receiver->email)
            ->set('amount', '10.00')
            ->call('transfer')
            ->assertHasNoErrors();
    }

    $component->set('recipient_email', $receiver->email)
        ->set('amount', '10.00')
        ->call('transfer')
        ->assertHasErrors('amount');

    // Apenas as 3 transferências permitidas debitaram o saldo.
    expect($sender->wallet->refresh()->balance_cents)->toBe(100000 - 3000);
});

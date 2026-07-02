<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

it('registra um novo usuário e cria a carteira automaticamente', function () {
    Livewire::test(Register::class)
        ->set('name', 'Nova Pessoa')
        ->set('email', 'nova@example.com')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'senha1234')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'nova@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->wallet)->not->toBeNull()
        ->and($user->wallet->balance_cents)->toBe(0);

    $this->assertAuthenticatedAs($user);
});

it('exige confirmação de senha', function () {
    Livewire::test(Register::class)
        ->set('name', 'Fulano')
        ->set('email', 'fulano@example.com')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'diferente')
        ->call('register')
        ->assertHasErrors('password');

    $this->assertGuest();
});

it('não permite e-mail duplicado', function () {
    User::factory()->create(['email' => 'dup@example.com']);

    Livewire::test(Register::class)
        ->set('name', 'Fulano')
        ->set('email', 'dup@example.com')
        ->set('password', 'senha1234')
        ->set('password_confirmation', 'senha1234')
        ->call('register')
        ->assertHasErrors('email');
});

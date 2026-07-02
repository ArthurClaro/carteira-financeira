<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('autentica com credenciais válidas', function () {
    $user = User::factory()->create([
        'email' => 'arthur@example.com',
        'password' => Hash::make('senha1234'),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'arthur@example.com')
        ->set('password', 'senha1234')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('rejeita credenciais inválidas', function () {
    User::factory()->create([
        'email' => 'arthur@example.com',
        'password' => Hash::make('senha1234'),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'arthur@example.com')
        ->set('password', 'senha-errada')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

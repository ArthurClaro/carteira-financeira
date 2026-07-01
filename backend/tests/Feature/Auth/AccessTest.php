<?php

use App\Models\User;

it('redireciona visitante do dashboard para o login', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
});

it('permite usuário autenticado acessar o dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertOk();
});

it('faz logout e invalida a sessão', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

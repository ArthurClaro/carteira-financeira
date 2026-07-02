<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Cada usuário nasce com uma carteira (relação 1–1). Fica dentro da mesma
     * transação de banco do cadastro (ver RegisterUserAction).
     */
    public function created(User $user): void
    {
        $user->wallet()->create([
            'balance_cents' => 0,
            'currency' => 'BRL',
        ]);
    }
}

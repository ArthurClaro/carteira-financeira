<?php

namespace App\Livewire\Concerns;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

trait InteractsWithCurrentWallet
{
    /**
     * Carteira do usuário autenticado. Garantidamente não-nula em contexto
     * autenticado (toda conta nasce com uma carteira via UserObserver).
     */
    protected function currentWallet(): Wallet
    {
        $wallet = Auth::user()?->wallet;

        abort_if($wallet === null, 403);

        return $wallet;
    }
}

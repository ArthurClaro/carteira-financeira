<?php

namespace App\Livewire\Wallet;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    /**
     * Re-renderiza o saldo quando qualquer operação dispara 'wallet-updated'.
     */
    #[On('wallet-updated')]
    public function refreshBalance(): void
    {
        // O saldo é relido em render(); o listener apenas força a atualização.
    }

    public function render()
    {
        return view('livewire.wallet.dashboard', [
            'wallet' => Auth::user()->wallet()->first(),
        ]);
    }
}

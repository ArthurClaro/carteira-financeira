<?php

namespace App\Livewire\Wallet;

use App\Livewire\Concerns\InteractsWithCurrentWallet;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use InteractsWithCurrentWallet;

    /**
     * Re-renderiza o saldo quando qualquer operação dispara 'wallet-updated'.
     */
    #[On('wallet-updated')]
    public function refreshBalance(): void
    {
        // O saldo é relido em render(); o listener apenas força a atualização.
    }

    public function render(): View
    {
        return view('livewire.wallet.dashboard', [
            'wallet' => $this->currentWallet(),
        ]);
    }
}

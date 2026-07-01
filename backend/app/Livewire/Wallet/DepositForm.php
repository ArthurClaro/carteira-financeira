<?php

namespace App\Livewire\Wallet;

use App\Exceptions\DomainException;
use App\Services\WalletService;
use App\Support\Money;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DepositForm extends Component
{
    #[Validate('required|numeric|gt:0|decimal:0,2')]
    public string $amount = '';

    public ?string $success = null;

    public function deposit(WalletService $wallet): void
    {
        $this->success = null;
        $this->validate();

        try {
            $transaction = $wallet->deposit(
                Auth::user()->wallet,
                Money::fromReais($this->amount)->cents(),
            );
        } catch (DomainException $e) {
            $this->addError('amount', $e->getMessage());

            return;
        }

        $this->success = "Depósito de {$transaction->amount()->format()} realizado.";
        $this->reset('amount');
        $this->dispatch('wallet-updated');
    }

    public function render()
    {
        return view('livewire.wallet.deposit-form');
    }
}

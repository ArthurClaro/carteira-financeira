<?php

namespace App\Livewire\Wallet;

use App\Exceptions\DomainException;
use App\Models\User;
use App\Services\WalletService;
use App\Support\Money;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TransferForm extends Component
{
    #[Validate('required|email')]
    public string $recipient_email = '';

    #[Validate('required|numeric|gt:0|decimal:0,2')]
    public string $amount = '';

    public ?string $success = null;

    public function transfer(WalletService $wallet): void
    {
        $this->success = null;
        $this->validate();

        $recipient = User::where('email', $this->recipient_email)->first();

        if ($recipient === null) {
            $this->addError('recipient_email', 'Nenhum usuário encontrado com este e-mail.');

            return;
        }

        if ($recipient->id === Auth::id()) {
            $this->addError('recipient_email', 'Não é possível transferir para si mesmo.');

            return;
        }

        try {
            $transaction = $wallet->transfer(
                Auth::user()->wallet,
                $recipient->wallet,
                Money::fromReais($this->amount)->cents(),
            );
        } catch (DomainException $e) {
            $this->addError('amount', $e->getMessage());

            return;
        }

        $this->success = "Transferência de {$transaction->amount()->format()} para {$recipient->name} realizada.";
        $this->reset('recipient_email', 'amount');
        $this->dispatch('wallet-updated');
    }

    public function render()
    {
        return view('livewire.wallet.transfer-form');
    }
}

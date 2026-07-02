<?php

namespace App\Livewire\Wallet;

use App\Exceptions\DomainException;
use App\Livewire\Concerns\InteractsWithCurrentWallet;
use App\Models\User;
use App\Services\WalletService;
use App\Support\Money;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TransferForm extends Component
{
    use InteractsWithCurrentWallet;

    #[Validate('required|email')]
    public string $recipient_email = '';

    #[Validate('required|numeric|gt:0|decimal:0,2')]
    public string $amount = '';

    public ?string $success = null;

    /** Chave de idempotência — impede que um duplo envio processe duas transferências. */
    public string $idempotencyKey = '';

    public function mount(): void
    {
        $this->idempotencyKey = (string) Str::uuid();
    }

    public function transfer(WalletService $service): void
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

        $recipientWallet = $recipient->wallet;
        abort_if($recipientWallet === null, 500);

        try {
            $transaction = $service->transfer(
                $this->currentWallet(),
                $recipientWallet,
                Money::fromReais($this->amount)->cents(),
                $this->idempotencyKey,
            );
        } catch (DomainException $e) {
            $this->addError('amount', $e->getMessage());

            return;
        }

        $this->success = "Transferência de {$transaction->amount()->format()} para {$recipient->name} realizada.";
        $this->reset('recipient_email', 'amount');
        $this->idempotencyKey = (string) Str::uuid(); // nova operação
        $this->dispatch('wallet-updated');
    }

    public function render(): View
    {
        return view('livewire.wallet.transfer-form');
    }
}

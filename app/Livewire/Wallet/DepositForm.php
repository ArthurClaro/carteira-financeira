<?php

namespace App\Livewire\Wallet;

use App\Exceptions\DomainException;
use App\Livewire\Concerns\InteractsWithCurrentWallet;
use App\Services\WalletService;
use App\Support\Money;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DepositForm extends Component
{
    use InteractsWithCurrentWallet;

    #[Validate('required|numeric|gt:0|decimal:0,2')]
    public string $amount = '';

    public ?string $success = null;

    /** Chave de idempotência — impede que um duplo envio processe dois depósitos. */
    public string $idempotencyKey = '';

    public function mount(): void
    {
        $this->idempotencyKey = (string) Str::uuid();
    }

    public function deposit(WalletService $service): void
    {
        $this->success = null;
        $this->validate();

        try {
            $transaction = $service->deposit(
                $this->currentWallet(),
                Money::fromReais($this->amount)->cents(),
                $this->idempotencyKey,
            );
        } catch (DomainException $e) {
            $this->addError('amount', $e->getMessage());

            return;
        }

        $this->success = "Depósito de {$transaction->amount()->format()} realizado.";
        $this->reset('amount');
        $this->idempotencyKey = (string) Str::uuid(); // nova operação
        $this->dispatch('wallet-updated');
    }

    public function render(): View
    {
        return view('livewire.wallet.deposit-form');
    }
}

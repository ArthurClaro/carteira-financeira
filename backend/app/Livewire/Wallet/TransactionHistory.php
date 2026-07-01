<?php

namespace App\Livewire\Wallet;

use App\Exceptions\DomainException;
use App\Models\Transaction;
use App\Services\WalletService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?string $flash = null;

    public bool $flashIsError = false;

    #[On('wallet-updated')]
    public function onWalletUpdated(): void
    {
        // Volta para a primeira página e re-renderiza o extrato atualizado.
        $this->resetPage();
    }

    public function reverse(string $uuid, WalletService $wallet): void
    {
        $this->flash = null;

        $transaction = Transaction::where('uuid', $uuid)->firstOrFail();

        $this->authorize('reverse', $transaction);

        try {
            $wallet->reverse($transaction);
        } catch (DomainException $e) {
            $this->flash = $e->getMessage();
            $this->flashIsError = true;

            return;
        }

        $this->flash = 'Transação estornada com sucesso.';
        $this->flashIsError = false;
        $this->dispatch('wallet-updated');
    }

    public function render()
    {
        $walletId = Auth::user()->wallet->id;

        $transactions = Transaction::query()
            ->where(fn ($q) => $q->where('from_wallet_id', $walletId)->orWhere('to_wallet_id', $walletId))
            ->latest()
            ->paginate(10);

        return view('livewire.wallet.transaction-history', [
            'transactions' => $transactions,
            'walletId' => $walletId,
        ]);
    }
}

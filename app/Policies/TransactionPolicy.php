<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Apenas o iniciador da transação pode estorná-la:
     *  - transferência: o remetente (dono da from_wallet);
     *  - depósito: o dono da carteira creditada (to_wallet, pois from é null).
     */
    public function reverse(User $user, Transaction $transaction): bool
    {
        $wallet = $user->wallet;

        if ($wallet === null) {
            return false;
        }

        $initiatorWalletId = $transaction->from_wallet_id ?? $transaction->to_wallet_id;

        return $wallet->id === $initiatorWalletId;
    }
}

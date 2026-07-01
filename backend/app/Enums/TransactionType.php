<?php

namespace App\Enums;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Transfer = 'transfer';
    case Reversal = 'reversal';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'Depósito',
            self::Transfer => 'Transferência',
            self::Reversal => 'Estorno',
        };
    }
}

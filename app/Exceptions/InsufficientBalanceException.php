<?php

namespace App\Exceptions;

class InsufficientBalanceException extends DomainException
{
    public function __construct(
        public readonly int $balanceCents,
        public readonly int $requestedCents,
    ) {
        parent::__construct('Saldo insuficiente para completar a transferência.');
    }
}

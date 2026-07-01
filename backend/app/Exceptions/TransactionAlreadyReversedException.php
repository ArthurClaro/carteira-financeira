<?php

namespace App\Exceptions;

class TransactionAlreadyReversedException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Esta transação já foi estornada.');
    }
}

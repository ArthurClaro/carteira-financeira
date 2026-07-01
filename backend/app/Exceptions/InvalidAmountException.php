<?php

namespace App\Exceptions;

class InvalidAmountException extends DomainException
{
    public function __construct()
    {
        parent::__construct('O valor deve ser maior que zero.');
    }
}

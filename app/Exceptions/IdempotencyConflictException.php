<?php

namespace App\Exceptions;

class IdempotencyConflictException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Esta chave de idempotência já foi usada em uma operação diferente.');
    }
}

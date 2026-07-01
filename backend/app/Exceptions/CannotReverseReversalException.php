<?php

namespace App\Exceptions;

class CannotReverseReversalException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Um estorno não pode ser estornado.');
    }
}

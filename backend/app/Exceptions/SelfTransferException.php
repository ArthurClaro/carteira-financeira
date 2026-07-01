<?php

namespace App\Exceptions;

class SelfTransferException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Não é possível transferir para a própria carteira.');
    }
}

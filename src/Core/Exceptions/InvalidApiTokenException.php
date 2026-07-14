<?php

namespace App\Core\Exceptions;

class InvalidApiTokenException extends \RuntimeException
{
    public function __construct(string $message = "Token inválido o revocado")
    {
        parent::__construct($message);
    }
}

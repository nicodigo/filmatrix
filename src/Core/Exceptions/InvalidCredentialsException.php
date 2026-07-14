<?php

namespace App\Core\Exceptions;

class InvalidCredentialsException extends \RuntimeException
{
    public function __construct(string $message = "Credenciales inválidas")
    {
        parent::__construct($message);
    }
}

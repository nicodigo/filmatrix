<?php

namespace App\Core\Exceptions;

class InvalidPasswordException extends \RuntimeException
{
    public function __construct(string $message = "Invalid password")
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Core\Exceptions;

class EmailAlreadyTakenException extends \RuntimeException
{
    public function __construct(string $message = "Email is already taken")
    {
        parent::__construct($message);
    }
}

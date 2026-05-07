<?php

namespace App\Core\Exceptions;

class UsernameAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $message = "Username already exists")
    {
        parent::__construct($message);
    }
}

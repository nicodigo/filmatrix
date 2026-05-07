<?php

namespace App\Core\Exceptions;

class UserNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "User not found")
    {
        parent::__construct($message);
    }
}

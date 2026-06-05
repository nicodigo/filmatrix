<?php

namespace App\Core\Exceptions;

class UnauthorizedListAccessException extends \RuntimeException
{
    public function __construct(string $message = "You do not have permission to access this list")
    {
        parent::__construct($message);
    }
}

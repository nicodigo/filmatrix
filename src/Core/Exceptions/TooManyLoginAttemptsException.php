<?php

namespace App\Core\Exceptions;

class TooManyLoginAttemptsException extends \RuntimeException
{
    public function __construct(string $message = "Too many login attempts")
    {
        parent::__construct($message);
    }
}

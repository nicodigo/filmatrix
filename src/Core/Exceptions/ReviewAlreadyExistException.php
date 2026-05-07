<?php

namespace App\Core\Exceptions;

class ReviewAlreadyExistException extends \RuntimeException
{
    public function __construct(string $message = "Review already exists")
    {
        parent::__construct($message);
    }
}

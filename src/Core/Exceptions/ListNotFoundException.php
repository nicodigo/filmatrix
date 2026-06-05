<?php

namespace App\Core\Exceptions;

class ListNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "List not found")
    {
        parent::__construct($message);
    }
}

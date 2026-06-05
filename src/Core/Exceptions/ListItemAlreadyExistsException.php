<?php

namespace App\Core\Exceptions;

class ListItemAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $message = "Item already exists in list")
    {
        parent::__construct($message);
    }
}

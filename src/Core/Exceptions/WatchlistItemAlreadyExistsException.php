<?php

namespace App\Core\Exceptions;

class WatchlistItemAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $message = "Watchlist item already exists")
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Core\Exceptions;

class WatchlistItemNotFoundException extends \RuntimeException
{
    public function __construct(string $message = "Watchlist item not found")
    {
        parent::__construct($message);
    }
}

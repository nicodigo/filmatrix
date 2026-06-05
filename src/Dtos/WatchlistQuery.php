<?php

namespace App\Dtos;

class WatchlistQuery
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly int     $page = 1,
    ) {}
}

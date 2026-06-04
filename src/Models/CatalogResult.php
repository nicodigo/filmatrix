<?php

namespace App\Models;

class CatalogResult
{
    public function __construct(
        public readonly array  $items,
        public readonly int    $currentPage,
        public readonly int    $totalPages,
        public readonly string $source, // 'tmdb' | 'local'
    ) {}

    public function hasNextPage(): bool { return $this->currentPage < $this->totalPages; }
    public function hasPrevPage(): bool { return $this->currentPage > 1; }
}

<?php

declare(strict_types=1);

namespace App\Dtos;

class ListDetailResult
{
    public function __construct(
        public readonly array  $items,
        public readonly int    $currentPage,
        public readonly int    $totalPages,
        public readonly string $listName,
        public readonly int    $listId,
        public readonly bool   $isPublic,
        public readonly bool   $isOwner,
    ) {}

    public function hasNextPage(): bool { return $this->currentPage < $this->totalPages; }
    public function hasPrevPage(): bool { return $this->currentPage > 1; }
}

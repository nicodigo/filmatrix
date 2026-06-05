<?php

declare(strict_types=1);

namespace App\Dtos;

class ListCardDto
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly bool   $isPublic,
        public readonly int    $itemCount,
        public readonly string $updatedAt,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Models;

class ListItem
{
    public function __construct(
        public readonly int    $listId,
        public readonly int    $titleId,
        public readonly string $addedAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            listId:  (int) $row['list_id'],
            titleId: (int) $row['title_id'],
            addedAt: $row['added_at'],
        );
    }
}

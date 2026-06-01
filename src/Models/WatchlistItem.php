<?php
declare(strict_types=1);

namespace App\Models;

class WatchlistItem
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $userId,
        public readonly int     $titleId,
        public readonly string  $status,       // pending | watching | watched
        public readonly string  $addedAt,
        public readonly string  $updatedAt,

    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            userId:      (int) $row['user_id'],
            titleId:     (int) $row['title_id'],
            status:      $row['status'],
            addedAt:     $row['added_at'],
            updatedAt:   $row['updated_at'],
        );
    }
}

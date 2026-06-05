<?php

declare(strict_types=1);

namespace App\Models;

class UserList
{
    public function __construct(
        public readonly int    $id,
        public readonly int    $userId,
        public readonly string $name,
        public readonly bool   $isPublic,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:        (int) $row['id'],
            userId:    (int) $row['user_id'],
            name:      $row['name'],
            isPublic:  (bool) $row['is_public'],
            createdAt: $row['created_at'],
            updatedAt: $row['updated_at'],
        );
    }
}

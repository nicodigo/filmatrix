<?php

declare(strict_types=1);

namespace App\Dtos;

class ListItemEntry
{
    public function __construct(
        public readonly int     $listId,
        public readonly int     $titleId,
        public readonly string  $addedAt,
        public readonly int     $tmdbId,
        public readonly string  $title,
        public readonly ?string $posterUrl,
        public readonly ?int    $releaseYear,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            listId:      (int) $row['list_id'],
            titleId:     (int) $row['title_id'],
            addedAt:     $row['added_at'],
            tmdbId:      (int) $row['tmdb_id'],
            title:       $row['title'],
            posterUrl:   $row['poster_url'] ?? null,
            releaseYear: isset($row['release_year']) ? (int) $row['release_year'] : null,
        );
    }
}

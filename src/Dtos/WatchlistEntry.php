<?php

declare(strict_types=1);

namespace App\Dtos;

class WatchlistEntry
{
    public function __construct(
        public readonly int     $watchlistItemId,
        public readonly string  $status,
        public readonly string  $addedAt,
        public readonly string  $updatedAt,
        public readonly int     $tmdbId,
        public readonly string  $title,
        public readonly ?string $posterUrl,
        public readonly ?int    $releaseYear,
        public readonly string  $type,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            watchlistItemId: (int) $row['id'],
            status:          $row['status'],
            addedAt:         $row['added_at'],
            updatedAt:       $row['updated_at'],
            tmdbId:          (int) $row['tmdb_id'],
            title:           $row['title'],
            posterUrl:       $row['poster_url'] ?? null,
            releaseYear:     isset($row['release_year']) ? (int) $row['release_year'] : null,
            type:            $row['type'],
        );
    }
}

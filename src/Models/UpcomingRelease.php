<?php

declare(strict_types=1);

namespace App\Models;

class UpcomingRelease
{
    public function __construct(
        public readonly int     $id,
        public readonly int     $tmdbId,
        public readonly string  $title,
        public readonly ?string $posterUrl,
        public readonly ?string $synopsis,
        public readonly string  $releaseDate, 
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            tmdbId:      (int) $row['tmdb_id'],
            title:       $row['title'],
            posterUrl:   $row['poster_url'] ?? null,
            synopsis:    $row['synopsis'] ?? null,
            releaseDate: $row['release_date'],
        );
    }
}
<?php

declare(strict_types=1);

namespace App\Dtos;

class TitleCardDto
{
    public function __construct(
        public readonly int     $tmdbId,
        public readonly string  $title,
        public readonly ?string $posterUrl,
        public readonly ?float  $avgScore,
        public readonly ?string $watchlistStatus = null,
    ) {}

    public static function fromTitle(\App\Models\Title $title): self
    {
        return new self(
            tmdbId:          $title->getTmdbId(),
            title:           $title->getTitle(),
            posterUrl:       $title->getPosterUrl(),
            avgScore:        $title->getAvgScore(),
        );
    }

    public static function fromWatchlistEntry(WatchlistEntry $entry): self
    {
        return new self(
            tmdbId:          $entry->tmdbId,
            title:           $entry->title,
            posterUrl:       $entry->posterUrl,
            avgScore:        null,
            watchlistStatus: $entry->status,
        );
    }
}

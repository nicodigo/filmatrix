<?php

namespace App\Models;

class Title
{
    private ?int $id;
    private int $tmdbId;
    private string $type;
    private string $title;
    private ?string $synopsis;
    private ?string $posterUrl;
    private ?string $trailerUrl;
    private ?int $releaseYear;
    private ?string $language;
    private ?int $durationMinutes;
    private ?float $tmdbRating;
    private ?float $avgScore;
    private ?string $cachedAt;

    public function __construct(
        ?int $id,
        int $tmdbId,
        string $type,
        string $title,
        ?string $synopsis = null,
        ?string $posterUrl = null,
        ?string $trailerUrl = null,
        ?int $releaseYear = null,
        ?string $language = null,
        ?int $durationMinutes = null,
        ?float $tmdbRating = null,
        ?float $avgScore = null,
        ?string $cachedAt = null
    ) {
        $this->id = $id;
        $this->tmdbId = $tmdbId;
        $this->type = in_array($type, ['movie', 'series'])
            ? $type
            : 'movie';

        $this->title = trim($title);
        $this->synopsis = $synopsis;
        $this->posterUrl = $posterUrl;
        $this->trailerUrl = $trailerUrl;
        $this->releaseYear = $releaseYear;
        $this->language = $language;
        $this->durationMinutes = $durationMinutes;
        $this->tmdbRating = $tmdbRating;
        $this->avgScore = $avgScore;
        $this->cachedAt = $cachedAt;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbId(): int
    {
        return $this->tmdbId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function getPosterUrl(): ?string
    {
        return $this->posterUrl;
    }

    public function getTrailerUrl(): ?string
    {
        return $this->trailerUrl;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function getTmdbRating(): ?float
    {
        return $this->tmdbRating;
    }

    public function getAvgScore(): ?float
    {
        return $this->avgScore;
    }

    public function getCachedAt(): ?string
    {
        return $this->cachedAt;
    }

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTmdbId(int $tmdbId): void
    {
        $this->tmdbId = $tmdbId;
    }

    public function setType(string $type): void
    {
        $this->type = in_array($type, ['movie', 'series'])
            ? $type
            : 'movie';
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function setSynopsis(?string $synopsis): void
    {
        $this->synopsis = $synopsis;
    }

    public function setPosterUrl(?string $posterUrl): void
    {
        $this->posterUrl = $posterUrl;
    }

    public function setTrailerUrl(?string $trailerUrl): void
    {
        $this->trailerUrl = $trailerUrl;
    }

    public function setReleaseYear(?int $releaseYear): void
    {
        $this->releaseYear = $releaseYear;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function setDurationMinutes(?int $durationMinutes): void
    {
        $this->durationMinutes = $durationMinutes;
    }

    public function setTmdbRating(?float $tmdbRating): void
    {
        $this->tmdbRating = $tmdbRating;
    }

    public function setAvgScore(?float $avgScore): void
    {
        $this->avgScore = $avgScore;
    }

    public function setCachedAt(?string $cachedAt): void
    {
        $this->cachedAt = $cachedAt;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isMovie(): bool
    {
        return $this->type === 'movie';
    }

    public function isSeries(): bool
    {
        return $this->type === 'series';
    }

    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['tmdb_id'],
            $data['type'],
            $data['title'],
            $data['synopsis'] ?? null,
            $data['poster_url'] ?? null,
            $data['trailer_url'] ?? null,
            isset($data['release_year'])
                ? (int) $data['release_year']
                : null,
            $data['language'] ?? null,
            isset($data['duration_minutes'])
                ? (int) $data['duration_minutes']
                : null,
            isset($data['tmdb_rating'])
                ? (float) $data['tmdb_rating']
                : null,
            isset($data['avg_score'])
                ? (float) $data['avg_score']
                : null,
            $data['cached_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tmdb_id' => $this->tmdbId,
            'type' => $this->type,
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'poster_url' => $this->posterUrl,
            'trailer_url' => $this->trailerUrl,
            'release_year' => $this->releaseYear,
            'language' => $this->language,
            'duration_minutes' => $this->durationMinutes,
            'tmdb_rating' => $this->tmdbRating,
            'avg_score' => $this->avgScore,
            'cached_at' => $this->cachedAt,
        ];
    }
}
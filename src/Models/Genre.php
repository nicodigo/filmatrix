<?php

namespace App\Models;

class Genre
{
    private ?int $id;
    private int $tmdbGenreId;
    private string $name;

    public function __construct(
        ?int $id,
        int $tmdbGenreId,
        string $name
    ) {
        $this->id = $id;
        $this->tmdbGenreId = $tmdbGenreId;
        $this->name = trim($name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbGenreId(): int
    {
        return $this->tmdbGenreId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTmdbGenreId(int $tmdbGenreId): void
    {
        $this->tmdbGenreId = $tmdbGenreId;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['tmdb_genre_id'],
            $data['name']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tmdb_genre_id' => $this->tmdbGenreId,
            'name' => $this->name,
        ];
    }
}
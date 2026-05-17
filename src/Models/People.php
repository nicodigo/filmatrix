<?php

namespace App\Models;

class People
{
    private ?int $id;
    private int $tmdbPersonId;
    private string $name;
    private string $cachedAt;

    public function __construct(
        ?int $id,
        int $tmdbPersonId,
        string $name,
        string $cachedAt
    ) {
        $this->id = $id;
        $this->tmdbPersonId = $tmdbPersonId;
        $this->name = $name;
        $this->cachedAt = $cachedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            (int) $data['tmdb_person_id'],
            $data['name'],
            $data['cached_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tmdb_person_id' => $this->tmdbPersonId,
            'name' => $this->name,
            'cached_at' => $this->cachedAt,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTmdbPersonId(): int
    {
        return $this->tmdbPersonId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCachedAt(): string
    {
        return $this->cachedAt;
    }
}

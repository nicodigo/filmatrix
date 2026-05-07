<?php

namespace App\Services;

use App\Models\People;
use App\Repository\PeopleRepository;

class PeopleService
{
    private PeopleRepository $peopleRepository;

    public function __construct(PeopleRepository $peopleRepository)
    {
        $this->peopleRepository = $peopleRepository;
    }

    public function findById(int $id): ?People
    {
        return $this->peopleRepository->findById($id);
    }

    public function findByTmdbId(int $tmdbPersonId): ?People
    {
        return $this->peopleRepository->findByTmdbId($tmdbPersonId);
    }

    /**
     * @return People[]
     */
    public function findAll(): array
    {
        return $this->peopleRepository->findAll();
    }

    /**
     * Upsert desde datos de TMDB y devuelve el ID interno.
     */
    public function sync(int $tmdbPersonId, string $name, ?string $profileUrl): int
    {
        return $this->peopleRepository->upsert($tmdbPersonId, $name, $profileUrl);
    }

    /**
     * Devuelve el cast completo de un título (actores + directores).
     */
    public function getCastByTitleId(int $titleId): array
    {
        return $this->peopleRepository->findCastByTitleId($titleId);
    }

    /**
     * Filtra el cast por rol: 'actor' | 'director'
     */
    public function getByRole(int $titleId, string $role): array
    {
        $cast = $this->peopleRepository->findCastByTitleId($titleId);

        return array_values(
            array_filter($cast, fn(array $member) => $member['role'] === $role)
        );
    }

    public function getActors(int $titleId): array
    {
        return $this->getByRole($titleId, 'actor');
    }

    public function getDirectors(int $titleId): array
    {
        return $this->getByRole($titleId, 'director');
    }
}
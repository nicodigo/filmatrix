<?php

namespace App\Services;

use App\Models\Genre;
use App\Repository\GenreRepository;

class GenreService
{
    private GenreRepository $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    /**
     * @return Genre[]
     */
    public function getAll(): array
    {
        return $this->genreRepository->findAll();
    }

    public function getById(int $id): ?Genre
    {
        return $this->genreRepository->findById($id);
    }

    public function getByTmdbId(int $tmdbGenreId): ?Genre
    {
        return $this->genreRepository->findByTmdbId($tmdbGenreId);
    }
}
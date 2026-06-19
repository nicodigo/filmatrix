<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\RecommendationRepository;
use App\Services\GenrePreferenceService;
use App\Services\GenreService;

/**
 * Motor de recomendaciones personalizadas.
 *
 * Orquesta la obtención de títulos rankeados y el flujo de descarte,
 * delegando la persistencia a los repositorios correspondientes y la
 * actualización de preferencias a GenrePreferenceService.
 */
class RecommendationService
{
    private const int DEFAULT_LIMIT = 20;
    private const int MIN_LIMIT     = 1;
    private const int MAX_LIMIT     = 50;

    public function __construct(
        private RecommendationRepository $recommendationRepository,
        private GenrePreferenceService   $preferenceService,
        private GenreService             $genreService,
    ) {}

    /**
     * Retorna hasta $limit títulos recomendados para el usuario,
     * ordenados por relevancia descendente.
     *
     * $limit se clampea a [MIN_LIMIT, MAX_LIMIT] para evitar cargas
     * arbitrarias a la base si en el futuro se expone por query param.
     *
     * @return array<int, array{
     *   id: int,
     *   tmdb_id: int,
     *   title: string,
     *   poster_url: string|null,
     *   release_year: int|null,
     *   rec_score: float,
     *   avg_score: float|null
     * }>
     */
    public function getRecommendations(int $userId, int $limit = self::DEFAULT_LIMIT): array
    {
        $limit = max(self::MIN_LIMIT, min(self::MAX_LIMIT, $limit));

        return $this->recommendationRepository->findRanked($userId, $limit);
    }

    /**
     * Descarta un título desde la sección de recomendaciones:
     *   1. Escribe en discarded_titles (excluye el título de futuras sugerencias).
     *   2. Decrementa los pesos de los géneros del título en el perfil del usuario.
     */
    public function discard(int $userId, int $titleId): void
    {
        $this->recommendationRepository->discard($userId, $titleId);
        $this->preferenceService->applyDiscard($userId, $titleId);
    }

    /**
     * Obtiene el top 3 de generos favoritos del usuario.
     *
     * @return array<int, float> genre_id => weight
     */
    public function getTopGenres(int $userId): array
    {
        return $this->preferenceService->getTopGenres($userId);
    }

    /**
     * Obtiene 12 peliculas recomendables para un genero particular
     */
    public function getRecommendationsByGenre(int $userId, int $genreId, int $limit = 12): array
    {
        return $this->recommendationRepository->findRankedByGenre($userId, $genreId, $limit);
    }

    /**
     * Devuelve 3 generos y 12 peliculas asociadas a cada genero para recomendarle al usuario
     */
    public function getGenreBasedRecommendations(int $userId): array
    {
        $genres = $this->getTopGenres($userId); // genre_id => weight

        $result = [];

        foreach ($genres as $genreId => $weight) {
            $genre = $this->genreService->getById($genreId);

            if ($genre === null) {
                continue;
            }

            $result[] = [
                'genre' => $genre->getName(),
                'titles' => $this->getRecommendationsByGenre(
                    $userId,
                    $genreId,
                    12
                ),
            ];
        }

        return $result;
    }
}
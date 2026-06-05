<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\RecommendationRepository;
use App\Repository\WatchlistRepository;

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

    public function __construct(
        private RecommendationRepository $recommendationRepository,
        private WatchlistRepository      $watchlistRepository,
        private GenrePreferenceService   $preferenceService,
    ) {}

    /**
     * Retorna hasta $limit títulos recomendados para el usuario,
     * ordenados por relevancia descendente.
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
        return $this->recommendationRepository->findRanked($userId, $limit);
    }

    /**
     * Descarta un título desde la sección de recomendaciones:
     *   1. Escribe en discarded_titles (excluye el título de futuras sugerencias).
     *   2. Decrementa los pesos de los géneros del título en el perfil del usuario.
     */
    public function discard(int $userId, int $titleId): void
    {
        $this->watchlistRepository->discard($userId, $titleId);
        $this->preferenceService->applyDiscard($userId, $titleId);
    }
}

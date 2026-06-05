<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\GenrePreferenceRepository;
use App\Repository\TitleRepository;

/**
 * Gestiona el perfil de gustos del usuario (user_genre_preferences).
 *
 * Los pesos se mantienen en el rango [0.0, 1.0] — el clamp se aplica
 * en la base de datos dentro de GenrePreferenceRepository::adjustWeight.
 *
 * Reglas de ajuste:
 *   Marcar como visto   → +DELTA_WATCHED  por género del título
 *   Descartar           → +DELTA_DISCARD  por género del título  (valor negativo)
 *   Reseña score 5      → +0.20 por género
 *   Reseña score 4      → +0.10 por género
 *   Reseña score 3      →  0    (sin cambio)
 *   Reseña score 2      → -0.10 por género
 *   Reseña score 1      → -0.20 por género
 *
 * Nota: createReview ya llama ensureWatched (delta de visto), por lo que
 * applyReview aplica únicamente el delta adicional de la puntuación.
 */
class GenrePreferenceService
{
    private const float DELTA_WATCHED = 0.10;
    private const float DELTA_DISCARD = -0.15;

    /** @var array<int, float> */
    private const array REVIEW_DELTAS = [
        1 => -0.20,
        2 => -0.10,
        3 =>  0.00,
        4 =>  0.10,
        5 =>  0.20,
    ];

    public function __construct(
        private GenrePreferenceRepository $prefRepository,
        private TitleRepository           $titleRepository,
    ) {}

    /**
     * Aplica el delta positivo de "visto" para todos los géneros del título.
     * Llamar cuando un usuario marca un título con status = 'watched'.
     */
    public function applyWatched(int $userId, int $titleId): void
    {
        $this->applyDeltaForTitle($userId, $titleId, self::DELTA_WATCHED);
    }

    /**
     * Aplica el delta negativo de "descartado" para todos los géneros del título.
     * Llamar cuando el usuario descarta una recomendación.
     */
    public function applyDiscard(int $userId, int $titleId): void
    {
        $this->applyDeltaForTitle($userId, $titleId, self::DELTA_DISCARD);
    }

    /**
     * Aplica el delta derivado de la puntuación de una reseña.
     * Score 3 no genera cambio.
     */
    public function applyReview(int $userId, int $titleId, float $score): void
    {
        $rounded = (int) round($score);
        $delta   = self::REVIEW_DELTAS[$rounded] ?? 0.0;

        if ($delta === 0.0) {
            return;
        }

        $this->applyDeltaForTitle($userId, $titleId, $delta);
    }

    // ── Privado ───────────────────────────────────────────────────────────────

    private function applyDeltaForTitle(int $userId, int $titleId, float $delta): void
    {
        $genres = $this->titleRepository->findGenresByTitleId($titleId);

        foreach ($genres as $genre) {
            $this->prefRepository->adjustWeight($userId, (int) $genre['id'], $delta);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/**
 * Consultas de recomendación personalizadas.
 *
 * Genera una lista de títulos rankeados para un usuario a partir de
 * los candidatos de la sección 'popular', ponderados por los pesos de
 * género almacenados en user_genre_preferences.
 *
 * Exclusiones aplicadas automáticamente:
 *   - Títulos que el usuario ya tiene en su watchlist (watchlist_items).
 *   - Títulos que el usuario descartó previamente (discarded_titles).
 *
 * Si el usuario aún no tiene preferencias registradas, todos los títulos
 * obtienen rec_score = 0 y el orden cae al positional de title_lists.
 */
class RecommendationRepository
{
    public function __construct(private PDO $db) {}

    /**
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
    public function findRanked(int $userId, int $limit): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                 t.id,
                 t.tmdb_id,
                 t.title,
                 t.poster_url,
                 t.release_year,
                 COALESCE(SUM(ugp.weight), 0)                     AS rec_score,
                 COALESCE(ROUND(AVG(r.score)::numeric, 1), NULL)  AS avg_score
             FROM title_lists tl
             JOIN  titles t   ON t.id  = tl.title_id
             JOIN  title_genres tg ON tg.title_id = t.id
             LEFT JOIN user_genre_preferences ugp
                 ON ugp.genre_id = tg.genre_id AND ugp.user_id = :uid_pref
             LEFT JOIN reviews r
                 ON r.title_id = t.id AND r.is_visible = true
             LEFT JOIN watchlist_items wi
                 ON wi.title_id = t.id AND wi.user_id = :uid_wl
             LEFT JOIN discarded_titles dt
                 ON dt.title_id = t.id AND dt.user_id = :uid_dt
             LEFT JOIN reviews r_exc
                 ON r_exc.title_id = t.id AND r_exc.user_id = :uid_rev
             WHERE tl.section = \'popular\'
               AND wi.id       IS NULL
               AND dt.title_id IS NULL
               AND r_exc.id    IS NULL
             GROUP BY t.id, t.tmdb_id, t.title, t.poster_url, t.release_year, tl.position
             ORDER BY rec_score DESC, tl.position ASC
             LIMIT :limit'
        );

        $stmt->bindValue(':uid_pref', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_wl',   $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_dt',   $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_rev',  $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',    $limit,  PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

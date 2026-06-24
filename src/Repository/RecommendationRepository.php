<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/**
 * Consultas de recomendación personalizadas.
 *
 * Genera una lista de títulos rankeados para un usuario a partir de
 * nuestro catalogo, ponderados por los pesos de
 * género almacenados en user_genre_preferences.
 *
 * rec_score es el PROMEDIO (no la suma) de los pesos de género del
 * título. Se eligió AVG sobre SUM para que una película con muchos
 * géneros mediocres no le gane a una con pocos géneros que el usuario
 * realmente prefiere.
 *
 * Exclusiones aplicadas automáticamente:
 *   - Títulos que el usuario ya tiene en su watchlist (watchlist_items).
 *   - Títulos que el usuario descartó previamente (discarded_titles).
 *   - Títulos que el usuario ya reseñó.
 *
 * Si el usuario aún no tiene preferencias registradas, todos los títulos
 * obtienen rec_score = 0 y el orden cae al positional de title_lists.
 *
 * title_genres se trae con LEFT JOIN: si un título de todavía
 * no tiene géneros sincronizados, igual aparece en el listado con
 * rec_score = 0 en vez de desaparecer silenciosamente.
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
                 COALESCE(AVG(ugp.weight), 0) AS rec_score,
                 COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
             FROM titles t
                LEFT JOIN title_genres tg ON tg.title_id = t.id
                LEFT JOIN user_genre_preferences ugp
                    ON ugp.genre_id = tg.genre_id AND ugp.user_id = :uid_pref
                LEFT JOIN reviews r
                    ON r.title_id = t.id AND r.is_visible = true
                LEFT JOIN watchlist_items wi
                    ON wi.title_id = t.id AND wi.user_id = :uid_wl
                LEFT JOIN discarded_titles dt
                    ON dt.title_id = t.id AND dt.user_id = :uid_dt
             WHERE wi.id       IS NULL
               AND dt.title_id IS NULL
               AND t.release_date <= CURRENT_DATE
               AND NOT EXISTS (
                   SELECT 1 FROM reviews r_exc
                   WHERE r_exc.title_id = t.id AND r_exc.user_id = :uid_rev
               )
             GROUP BY t.id, t.tmdb_id, t.title, t.poster_url, t.release_year, t.tmdb_vote_average
             ORDER BY rec_score DESC, avg_score DESC
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

    // ── Discarded titles ─────────────────────────────────────────────────────
    /**
     * Registra un título como descartado por el usuario.
     * Idempotente: si ya existía la fila, no hace nada.
     */
    public function discard(int $userId, int $titleId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO discarded_titles (user_id, title_id, discarded_at)
             VALUES (:user_id, :title_id, NOW())
             ON CONFLICT DO NOTHING'
        );
        $stmt->execute(['user_id' => $userId, 'title_id' => $titleId]);
    }

    /**
     * Verifica si el usuario descartó un título.
     */
    public function isDiscarded(int $userId, int $titleId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM discarded_titles
             WHERE user_id = :user_id AND title_id = :title_id'
        );
        $stmt->execute(['user_id' => $userId, 'title_id' => $titleId]);

        return $stmt->fetchColumn() !== false;
    }

     /**
     * A partir de un genero y un usuario, obtiene 12 peliculas para recomendarle.
     */
    public function findRankedByGenre(int $userId, int $genreId, int $limit = 12): array
    {
        $stmt = $this->db->prepare("
            SELECT
                t.id,
                t.tmdb_id,
                t.title,
                t.poster_url,
                t.release_year,
                COALESCE(AVG(ugp.weight), 0) AS rec_score,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
            FROM titles t
            JOIN title_genres tg ON tg.title_id = t.id

            LEFT JOIN user_genre_preferences ugp
                ON ugp.genre_id = tg.genre_id AND ugp.user_id = :uid_pref

            LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true

            LEFT JOIN watchlist_items wi
                ON wi.title_id = t.id AND wi.user_id = :uid_wl

            LEFT JOIN discarded_titles dt
                ON dt.title_id = t.id AND dt.user_id = :uid_dt

            WHERE tg.genre_id = :genre_id
            AND wi.id IS NULL
            AND dt.title_id IS NULL
            AND t.release_date <= CURRENT_DATE
            AND NOT EXISTS (
                SELECT 1 FROM reviews r_exc
                WHERE r_exc.title_id = t.id AND r_exc.user_id = :uid_rev
            )

            GROUP BY t.id, t.tmdb_id, t.title, t.poster_url, t.release_year
            ORDER BY rec_score DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':uid_pref', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_wl', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_dt', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_rev', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':genre_id', $genreId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findReplacementByGenre(int $userId, int $genreId, array $excludeIds): ?array
    {
        $excludeIds = array_filter($excludeIds);

        $excludeClause = '';
        $excludeParams = [];

        if (!empty($excludeIds)) {
            $namedKeys = [];
            foreach (array_values($excludeIds) as $i => $id) {
                $key = ":exclude_{$i}";
                $namedKeys[] = $key;
                $excludeParams[$key] = $id;
            }
            $excludeClause = 'AND t.id NOT IN (' . implode(',', $namedKeys) . ')';
        }

        $sql = "
            SELECT
                t.id,
                t.tmdb_id,
                t.title,
                t.poster_url,
                t.release_year,
                COALESCE(AVG(ugp.weight), 0) AS rec_score,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
            FROM titles t
            JOIN title_genres tg ON tg.title_id = t.id

            LEFT JOIN user_genre_preferences ugp
                ON ugp.genre_id = tg.genre_id AND ugp.user_id = :uid_pref

            LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true

            LEFT JOIN watchlist_items wi
                ON wi.title_id = t.id AND wi.user_id = :uid_wl

            LEFT JOIN discarded_titles dt
                ON dt.title_id = t.id AND dt.user_id = :uid_dt

            WHERE tg.genre_id = :genre_id
            AND wi.id IS NULL
            AND dt.title_id IS NULL
            AND t.release_date <= CURRENT_DATE
            {$excludeClause}
            AND NOT EXISTS (
                SELECT 1 FROM reviews r_exc
                WHERE r_exc.title_id = t.id AND r_exc.user_id = :uid_rev
            )

            GROUP BY t.id, t.tmdb_id, t.title, t.poster_url, t.release_year
            ORDER BY rec_score DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':uid_pref', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_wl', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_dt', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_rev', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':genre_id', $genreId, PDO::PARAM_INT);

        foreach ($excludeParams as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findReplacement(int $userId, array $excludeIds): ?array
    {
        $excludeIds = array_filter($excludeIds);

        $excludeClause = '';
        $excludeParams = [];

        if (!empty($excludeIds)) {
            $namedKeys = [];
            foreach (array_values($excludeIds) as $i => $id) {
                $key = ":exclude_{$i}";
                $namedKeys[] = $key;
                $excludeParams[$key] = $id;
            }
            $excludeClause = 'AND t.id NOT IN (' . implode(',', $namedKeys) . ')';
        }

        $sql = "
            SELECT
                t.id,
                t.tmdb_id,
                t.title,
                t.poster_url,
                t.release_year,
                COALESCE(AVG(ugp.weight), 0) AS rec_score,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
            FROM titles t
                LEFT JOIN title_genres tg ON tg.title_id = t.id
                LEFT JOIN user_genre_preferences ugp
                    ON ugp.genre_id = tg.genre_id AND ugp.user_id = :uid_pref
                LEFT JOIN reviews r
                    ON r.title_id = t.id AND r.is_visible = true
                LEFT JOIN watchlist_items wi
                    ON wi.title_id = t.id AND wi.user_id = :uid_wl
                LEFT JOIN discarded_titles dt
                    ON dt.title_id = t.id AND dt.user_id = :uid_dt
            WHERE wi.id       IS NULL
            AND dt.title_id IS NULL
            AND t.release_date <= CURRENT_DATE
            {$excludeClause}
            AND NOT EXISTS (
                SELECT 1 FROM reviews r_exc
                WHERE r_exc.title_id = t.id AND r_exc.user_id = :uid_rev
            )
            GROUP BY t.id, t.tmdb_id, t.title, t.poster_url, t.release_year, t.tmdb_vote_average
            ORDER BY rec_score DESC, avg_score DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':uid_pref', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_wl', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_dt', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':uid_rev', $userId, PDO::PARAM_INT);

        foreach ($excludeParams as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
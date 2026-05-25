<?php

/**
 * TitleRepository
 * Acceso a datos de la tabla titles y sus relaciones con géneros (title_genres)
 * y elenco (title_cast).
 *
 * MÉTODOS:
 *   findByTmdbId(tmdbId): ?Title
 *     Busca un título por su ID de TMDB. Retorna null si no existe.
 * 
 *   upsert(title): int
 *     Inserta un título o actualiza todos sus campos si ya existe el tmdb_id.
 *     Actualiza el timestamp de caché. Retorna el id interno del registro.
 * 
 *   search(query, limit): array
 *     Busca títulos cuyo nombre coincida parcialmente con el texto ingresado.
 *     La búsqueda es case-insensitive usando ILIKE e incluye el promedio
 *     de score de reseñas visibles.
 *
 *     Los resultados se ordenan por año de estreno descendente y se limita
 *     la cantidad de registros retornados.
 *
 * RELACIONES:
 *   clearGenres(titleId)
 *     Elimina todos los géneros asociados a un título. Usado antes de
 *     resincronizar géneros.
 *
 *   attachGenre(titleId, genreId)
 *     Asocia un género a un título. Ignora si la relación ya existe.
 *
 *   findGenresByTitleId(titleId): array
 *     Retorna todos los géneros de un título ordenados alfabéticamente.
 *
 *   clearCast(titleId)
 *     Elimina todo el elenco asociado a un título. Usado antes de
 *     resincronizar el cast.
 *
 *   attachCastMember(titleId, personId, role, characterName, billingOrder)
 *     Asocia una persona al elenco de un título con su rol, nombre de
 *     personaje y orden de crédito. Ignora si la relación ya existe.
 *
 *   findCastByTitleId(titleId): array
 *     Retorna el elenco completo de un título incluyendo nombre, foto,
 *     rol, personaje y orden de crédito, ordenado por billing_order ascendente.
 *
 * DEPENDENCIAS:
 *   PDO   — conexión a la base de datos.
 *   Title — modelo mapeado desde los resultados de la consulta.
 */

namespace App\Repository;

use App\Models\Title;
use PDO;

class TitleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByTmdbId(int $tmdbId, int $ttl_days): ?Title
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                t.*,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), NULL) AS avg_score
             FROM titles t
             LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true
             WHERE t.tmdb_id = :tmdb_id
             AND t.cached_at > NOW() - MAKE_INTERVAL(days := :ttl_days)
             GROUP BY t.id
             LIMIT 1"
        );

        $stmt->execute([
            ':tmdb_id' => $tmdbId,
            ':ttl_days' => $ttl_days,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Title::fromArray($row) : null;
    }

    public function upsert(Title $title): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO titles
                (tmdb_id, type, title, synopsis, poster_url, trailer_url,
                 release_year, language, duration_minutes, cached_at)
             VALUES
                (:tmdb_id, :type, :title, :synopsis, :poster_url, :trailer_url,
                 :release_year, :language, :duration_minutes, NOW())
             ON CONFLICT (tmdb_id)
             DO UPDATE SET
                type = EXCLUDED.type,
                title = EXCLUDED.title,
                synopsis = EXCLUDED.synopsis,
                poster_url = EXCLUDED.poster_url,
                trailer_url = EXCLUDED.trailer_url,
                release_year = EXCLUDED.release_year,
                language = EXCLUDED.language,
                duration_minutes = EXCLUDED.duration_minutes,
                cached_at = NOW()
             RETURNING id'
        );

        $stmt->execute([
            ':tmdb_id' => $title->getTmdbId(),
            ':type' => $title->getType(),
            ':title' => $title->getTitle(),
            ':synopsis' => $title->getSynopsis(),
            ':poster_url' => $title->getPosterUrl(),
            ':trailer_url' => $title->getTrailerUrl(),
            ':release_year' => $title->getReleaseYear(),
            ':language' => $title->getLanguage(),
            ':duration_minutes' => $title->getDurationMinutes(),
        ]);

        return (int) $stmt->fetchColumn();
    }

    
    public function search(string $query, int $limit = 20): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                t.*,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), NULL) AS avg_score
            FROM titles t
            LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true
            WHERE t.title ILIKE :query
            GROUP BY t.id
            ORDER BY t.release_year DESC NULLS LAST
            LIMIT :limit"
        );

        $stmt->execute([
            ':query' => '%' . $query . '%',
            ':limit' => $limit,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(fn($row) => Title::fromArray($row), $rows);
    }

    /* =========================
       RELACIONES
    ========================= */

    public function clearGenres(int $titleId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM title_genres WHERE title_id = :title_id'
        );

        $stmt->execute([':title_id' => $titleId]);
    }

    public function attachGenre(int $titleId, int $genreId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO title_genres (title_id, genre_id)
             VALUES (:title_id, :genre_id)
             ON CONFLICT DO NOTHING'
        );

        $stmt->execute([
            ':title_id' => $titleId,
            ':genre_id' => $genreId,
        ]);
    }

    public function findGenresByTitleId(int $titleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT g.id, g.name
             FROM title_genres tg
             JOIN genres g ON g.id = tg.genre_id
             WHERE tg.title_id = :title_id
             ORDER BY g.name ASC'
        );

        $stmt->execute([':title_id' => $titleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function clearCast(int $titleId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM title_cast WHERE title_id = :title_id'
        );

        $stmt->execute([':title_id' => $titleId]);
    }

    public function attachCastMember(
        int $titleId,
        int $personId,
        string $role,
        ?string $characterName,
        int $billingOrder
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO title_cast
                (title_id, person_id, role, character_name, billing_order)
             VALUES
                (:title_id, :person_id, :role, :character_name, :billing_order)
             ON CONFLICT DO NOTHING'
        );

        $stmt->execute([
            ':title_id' => $titleId,
            ':person_id' => $personId,
            ':role' => $role,
            ':character_name' => $characterName,
            ':billing_order' => $billingOrder,
        ]);
    }

    public function findCastByTitleId(int $titleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                p.id,
                p.name,
                tc.role,
                tc.character_name,
                tc.billing_order
             FROM title_cast tc
             JOIN people p ON p.id = tc.person_id
             WHERE tc.title_id = :title_id
             ORDER BY tc.billing_order ASC'
        );

        $stmt->execute([':title_id' => $titleId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}


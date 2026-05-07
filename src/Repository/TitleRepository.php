<?php

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

    public function findByTmdbId(int $tmdbId): ?Title
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM titles WHERE tmdb_id = :tmdb_id LIMIT 1'
        );

        $stmt->execute([':tmdb_id' => $tmdbId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Title::fromArray($row) : null;
    }

    public function findByTmdbIdWithScore(int $tmdbId): ?Title
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                t.*,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), NULL) AS avg_score
             FROM titles t
             LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true
             WHERE t.tmdb_id = :tmdb_id
             GROUP BY t.id
             LIMIT 1'
        );

        $stmt->execute([':tmdb_id' => $tmdbId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Title::fromArray($row) : null;
    }

    public function upsert(Title $title): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO titles
                (tmdb_id, type, title, synopsis, poster_url, trailer_url,
                 release_year, language, duration_minutes, tmdb_rating, cached_at)
             VALUES
                (:tmdb_id, :type, :title, :synopsis, :poster_url, :trailer_url,
                 :release_year, :language, :duration_minutes, :tmdb_rating, NOW())
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
                tmdb_rating = EXCLUDED.tmdb_rating,
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
            ':tmdb_rating' => $title->getTmdbRating(),
        ]);

        return (int) $stmt->fetchColumn();
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
                p.profile_url,
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
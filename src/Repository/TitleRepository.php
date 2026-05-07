<?php

namespace App\Repository;

use PDO;

class TitleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByTmdbId(int $tmdbId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM titles WHERE tmdb_id = :tmdb_id LIMIT 1'
        );

        $stmt->execute([
            ':tmdb_id' => $tmdbId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $row;
    }

    public function upsert(array $data): int
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
            ':tmdb_id' => $data['tmdb_id'],
            ':type' => $data['type'],
            ':title' => $data['title'],
            ':synopsis' => $data['synopsis'],
            ':poster_url' => $data['poster_url'],
            ':trailer_url' => $data['trailer_url'],
            ':release_year' => $data['release_year'],
            ':language' => $data['language'],
            ':duration_minutes' => $data['duration_minutes'],
            ':tmdb_rating' => $data['tmdb_rating'],
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['id'];
    }

    public function clearGenres(int $titleId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM title_genres WHERE title_id = :title_id'
        );

        $stmt->execute([
            ':title_id' => $titleId,
        ]);
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

    public function clearCast(int $titleId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM title_cast WHERE title_id = :title_id'
        );

        $stmt->execute([
            ':title_id' => $titleId,
        ]);
    }

    public function attachCastMember(int $titleId, int $personId, string $role, ?string $characterName, int $billingOrder): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO title_cast (title_id, person_id, role, character_name, billing_order)
             VALUES (:title_id, :person_id, :role, :character_name, :billing_order)
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

    public function findByTmdbIdWithScore(int $tmdbId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                 t.*,
                 COALESCE(ROUND(AVG(r.score)::numeric, 1), NULL) AS avg_score
             FROM titles t
             LEFT JOIN reviews r ON r.title_id = t.id AND r.is_visible = true
             WHERE t.tmdb_id = :tmdb_id
             GROUP BY t.id
             LIMIT 1'
        );

        $stmt->execute([
            ':tmdb_id' => $tmdbId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $row;
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

        $stmt->execute([
            ':title_id' => $titleId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows ?: [];
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

        $stmt->execute([
            ':title_id' => $titleId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows ?: [];
    }
}

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
            "SELECT
                t.*,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
            FROM titles t
            LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true
            WHERE t.tmdb_id = :tmdb_id
            GROUP BY t.id
            LIMIT 1"
        );

        $stmt->execute([
            ':tmdb_id' => $tmdbId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Title::fromArray($row) : null;
    }

    public function findAvgScoresForTmdbIds(array $tmdbIds): array
    {
        if (empty($tmdbIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($tmdbIds), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT
                t.tmdb_id,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
            FROM titles t
            LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true
            WHERE t.tmdb_id IN ($placeholders)
            GROUP BY t.tmdb_id, t.tmdb_vote_average"
        );

        $stmt->execute(array_values($tmdbIds));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $scores = [];
        foreach ($rows as $row) {
            $scores[(int) $row['tmdb_id']] = $row['avg_score'] !== null ? (float) $row['avg_score'] : null;
        }

        return $scores;
    }

    public function upsert(Title $title): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO titles
                (tmdb_id, type, title, synopsis, poster_url, trailer_url,
                release_year, release_date, language, duration_minutes, tmdb_vote_average, cached_at)
            VALUES
                (:tmdb_id, :type, :title, :synopsis, :poster_url, :trailer_url,
                :release_year, :release_date, :language, :duration_minutes, :tmdb_vote_average, NOW())
            ON CONFLICT (tmdb_id)
            DO UPDATE SET
                type = EXCLUDED.type,
                title = EXCLUDED.title,
                synopsis = EXCLUDED.synopsis,
                poster_url = EXCLUDED.poster_url,
                trailer_url = EXCLUDED.trailer_url,
                release_year = EXCLUDED.release_year,
                release_date = EXCLUDED.release_date,
                language = EXCLUDED.language,
                duration_minutes = EXCLUDED.duration_minutes,
                tmdb_vote_average = EXCLUDED.tmdb_vote_average,
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
            ':release_date' => $title->getReleaseDate(),
            ':language' => $title->getLanguage(),
            ':duration_minutes' => $title->getDurationMinutes(),
            ':tmdb_vote_average' => $title->getTmdbVoteAverage(),
        ]);

        return (int) $stmt->fetchColumn();
    }


    public function search(string $query, int $limit = 20): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                t.*,
                COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score
            FROM titles t
            LEFT JOIN reviews r
                ON r.title_id = t.id AND r.is_visible = true
            WHERE t.title ILIKE :query
            AND t.release_date <= CURRENT_DATE
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

    public function filter(
        ?int $genreId,
        ?int $year,
        ?string $language,
        ?float $minScore,
        int $limit = 20,
        int $offset = 0,
        string $orderBy = 'release_year'
    ): array {
        $conditions = [
            '1=1',
            't.release_date <= CURRENT_DATE',
        ];
        $params = [];

        if ($genreId !== null) {
            $conditions[] = 'EXISTS (
                SELECT 1 FROM title_genres tg
                WHERE tg.title_id = t.id AND tg.genre_id = :genre_id
            )';
            $params[':genre_id'] = $genreId;
        }

        if ($year !== null) {
            $conditions[] = 't.release_year = :year';
            $params[':year'] = $year;
        }

        if ($language !== null) {
            $conditions[] = 't.language = :language';
            $params[':language'] = $language;
        }

        $having = '';
        if ($minScore !== null) {
            $having = 'HAVING COALESCE(ROUND(AVG(r.score)::numeric, 1), 0) >= :min_score';
            $params[':min_score'] = $minScore;
        }

        $orderClauses = [
            'release_year' => 'ORDER BY t.release_year DESC NULLS LAST',
            'popularity'   => 'ORDER BY popularity DESC',
            'avg_score'    => 'ORDER BY avg_score DESC NULLS LAST',
        ];
        $orderSql = $orderClauses[$orderBy] ?? $orderClauses['release_year'];

        $where = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare(
            "SELECT
            t.*,
            COALESCE(ROUND(AVG(r.score)::numeric, 1), t.tmdb_vote_average) AS avg_score,
            COUNT(DISTINCT CASE WHEN r.created_at >= NOW() - INTERVAL '30 days' THEN r.id END) * 3
                + COUNT(DISTINCT CASE WHEN wi.status = 'watched' THEN wi.id END) * 2
                + COUNT(DISTINCT CASE WHEN wi.status = 'pending' THEN wi.id END) * 1
                + COUNT(DISTINCT li.list_id) * 1.5
                AS popularity
            FROM titles t
            LEFT JOIN reviews r ON r.title_id = t.id AND r.is_visible = true
            LEFT JOIN watchlist_items wi ON wi.title_id = t.id
            LEFT JOIN list_items li ON li.title_id = t.id
            WHERE {$where}
            GROUP BY t.id
            {$having}
            {$orderSql}
            LIMIT {$limit} OFFSET {$offset}"
        );

        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return array_map(fn($row) => Title::fromArray($row), $rows);
    }

    public function filterCount(
        ?int $genreId,
        ?int $year,
        ?string $language,
        ?float $minScore
    ): int {
        $conditions = [
            '1=1',
            't.release_date <= CURRENT_DATE',
        ];
        $params = [];

        if ($genreId !== null) {
            $conditions[] = 'EXISTS (
            SELECT 1 FROM title_genres tg
            WHERE tg.title_id = t.id AND tg.genre_id = :genre_id
        )';
            $params[':genre_id'] = $genreId;
        }

        if ($year !== null) {
            $conditions[] = 't.release_year = :year';
            $params[':year'] = $year;
        }

        if ($language !== null) {
            $conditions[] = 't.language = :language';
            $params[':language'] = $language;
        }

        $having = '';
        if ($minScore !== null) {
            $having = 'HAVING COALESCE(ROUND(AVG(r.score)::numeric, 1), 0) >= :min_score';
            $params[':min_score'] = $minScore;
        }

        $where = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM (
            SELECT t.id
            FROM titles t
            LEFT JOIN reviews r ON r.title_id = t.id AND r.is_visible = true
            WHERE {$where}
            GROUP BY t.id
            {$having}
        ) AS filtered"
        );

        $stmt->execute($params);
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
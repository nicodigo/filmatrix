<?php

namespace App\Repository;

use PDO;

class PeopleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByTmdbId(int $tmdbPersonId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM people WHERE tmdb_person_id = :tmdb_person_id LIMIT 1'
        );

        $stmt->execute([
            ':tmdb_person_id' => $tmdbPersonId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $row;
    }

    public function upsert(int $tmdbPersonId, string $name, ?string $profileUrl): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO people (tmdb_person_id, name, profile_url, cached_at)
             VALUES (:tmdb_person_id, :name, :profile_url, NOW())
             ON CONFLICT (tmdb_person_id)
             DO UPDATE SET
                name = EXCLUDED.name,
                profile_url = EXCLUDED.profile_url,
                cached_at = NOW()
             RETURNING id'
        );

        $stmt->execute([
            ':tmdb_person_id' => $tmdbPersonId,
            ':name' => $name,
            ':profile_url' => $profileUrl,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['id'];
    }
}

<?php

namespace App\Repository;

use PDO;
use App\Models\People;

class PeopleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByTmdbId(int $tmdbPersonId): ?People
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM people
             WHERE tmdb_person_id = :tmdb_person_id
             LIMIT 1'
        );

        $stmt->execute([
            ':tmdb_person_id' => $tmdbPersonId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return People::fromArray($row);
    }

    public function findById(int $id): ?People
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM people
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            ':id' => $id,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return People::fromArray($row);
    }

    /**
     * @return People[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM people
             ORDER BY name ASC'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            return [];
        }

        return array_map(
            fn(array $row) => People::fromArray($row),
            $rows
        );
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

    public function findCastByTitleId(int $titleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, tc.role, tc.character_name, tc.billing_order
            FROM people p
            JOIN title_cast tc ON tc.person_id = p.id
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
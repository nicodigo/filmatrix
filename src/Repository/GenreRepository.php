<?php

namespace App\Repository;

use PDO;

class GenreRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByTmdbId(int $tmdbGenreId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM genres WHERE tmdb_genre_id = :tmdb_genre_id LIMIT 1'
        );

        $stmt->execute([
            ':tmdb_genre_id' => $tmdbGenreId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $row;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM genres ORDER BY name ASC');

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows ?: [];
    }

    public function upsert(int $tmdbGenreId, string $name): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO genres (tmdb_genre_id, name)
             VALUES (:tmdb_genre_id, :name)
             ON CONFLICT (tmdb_genre_id)
             DO UPDATE SET name = EXCLUDED.name
             RETURNING id'
        );

        $stmt->execute([
            ':tmdb_genre_id' => $tmdbGenreId,
            ':name' => $name,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['id'];
    }
}

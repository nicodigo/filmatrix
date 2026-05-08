<?php
/**
 * GenreRepository
 * Acceso a datos de la tabla genres y su relación con títulos (title_genres).
 *
 * MÉTODOS:
 *   findByTmdbId(tmdbGenreId): ?Genre
 *     Busca un género por su ID de TMDB. Retorna null si no existe.
 *
 *   findAll(): Genre[]
 *     Retorna todos los géneros ordenados alfabéticamente por nombre.
 *
 *   upsert(tmdbGenreId, name): int
 *     Inserta un género o actualiza su nombre si ya existe el tmdb_genre_id.
 *     Retorna el id interno del registro.
 *
 *   findById(id): ?Genre
 *     Busca un género por su id interno. Retorna null si no existe.
 *
 *   findByTitleId(titleId): Genre[]
 *     Retorna todos los géneros asociados a un título via la tabla title_genres.
 *
 * DEPENDENCIAS:
 *   PDO   — conexión a la base de datos.
 *   Genre — modelo mapeado desde los resultados de la consulta.
 */

namespace App\Repository;

use PDO;
use App\Models\Genre;

class GenreRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByTmdbId(int $tmdbGenreId): ?Genre
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM genres
             WHERE tmdb_genre_id = :tmdb_genre_id
             LIMIT 1'
        );

        $stmt->execute([
            ':tmdb_genre_id' => $tmdbGenreId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Genre::fromArray($row);
    }

    /**
     * @return Genre[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM genres
             ORDER BY name ASC'
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            return [];
        }

        return array_map(
            fn(array $row) => Genre::fromArray($row),
            $rows
        );
    }

    public function upsert(int $tmdbGenreId, string $name): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO genres (tmdb_genre_id, name)
             VALUES (:tmdb_genre_id, :name)
             ON CONFLICT (tmdb_genre_id)
             DO UPDATE SET
                name = EXCLUDED.name
             RETURNING id'
        );

        $stmt->execute([
            ':tmdb_genre_id' => $tmdbGenreId,
            ':name' => $name,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $row['id'];
    }

    public function findById(int $id): ?Genre
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM genres
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

        return Genre::fromArray($row);
    }

    public function findByTitleId(int $titleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT g.*
            FROM genres g
            JOIN title_genres tg ON tg.genre_id = g.id
            WHERE tg.title_id = :title_id'
        );

        $stmt->execute([
            ':title_id' => $titleId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn($r) => Genre::fromArray($r),
            $rows ?: []
        );
}
}
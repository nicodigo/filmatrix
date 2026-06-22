<?php
/**
 * UpcomingReleaseRepository
 * Acceso a datos de la tabla upcoming_releases.
 *
 * MÉTODOS:
 *   clearAll()
 *     Elimina todos los registros. Usado antes de resincronizar
 *     desde TMDB.
 *
 *   upsert(tmdbId, title, posterUrl, synopsis, releaseDate)
 *     Inserta un estreno futuro o actualiza sus datos si ya existe
 *     el tmdb_id. Retorna el id interno del registro.
 *
 *   findAll()
 *     Retorna todos los estrenos futuros ordenados por fecha de
 *     estreno ascendente.
 *
 *   findByDate(date)
 *     Retorna los estrenos cuya fecha de estreno coincide exactamente
 *     con la fecha dada (formato Y-m-d).
 *
 *   findGroupedByMonth(year, month)
 *     Retorna los estrenos del mes/año dado, ordenados por fecha
 *     ascendente. Usado para poblar la grilla del calendario.
 *
 * DEPENDENCIAS:
 *   PDO              — conexión a la base de datos.
 *   UpcomingRelease   — modelo mapeado desde los resultados de la consulta.
 */

namespace App\Repository;

use PDO;
use App\Models\UpcomingRelease;

class UpcomingReleaseRepository
{
    public function __construct(private PDO $db) {}

    public function clearAll(): void
    {
        $this->db->exec('DELETE FROM upcoming_releases');
    }

    public function upsert(
        int $tmdbId,
        string $title,
        ?string $posterUrl,
        ?string $synopsis,
        string $releaseDate
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO upcoming_releases
                (tmdb_id, title, poster_url, synopsis, release_date, synced_at)
             VALUES
                (:tmdb_id, :title, :poster_url, :synopsis, :release_date, NOW())
             ON CONFLICT (tmdb_id)
             DO UPDATE SET
                title        = EXCLUDED.title,
                poster_url   = EXCLUDED.poster_url,
                synopsis     = EXCLUDED.synopsis,
                release_date = EXCLUDED.release_date,
                synced_at    = NOW()
             RETURNING id'
        );

        $stmt->execute([
            ':tmdb_id'      => $tmdbId,
            ':title'        => $title,
            ':poster_url'   => $posterUrl,
            ':synopsis'     => $synopsis,
            ':release_date' => $releaseDate,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /** @return UpcomingRelease[] */
    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM upcoming_releases ORDER BY release_date ASC'
        );

        return array_map(
            fn(array $row) => UpcomingRelease::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** @return UpcomingRelease[] */
    public function findByDate(string $date): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM upcoming_releases
             WHERE release_date = :date
             ORDER BY title ASC'
        );
        $stmt->execute([':date' => $date]);

        return array_map(
            fn(array $row) => UpcomingRelease::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** @return UpcomingRelease[] */
    public function findGroupedByMonth(int $year, int $month): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM upcoming_releases
             WHERE EXTRACT(YEAR FROM release_date) = :year
               AND EXTRACT(MONTH FROM release_date) = :month
             ORDER BY release_date ASC"
        );
        $stmt->execute([':year' => $year, ':month' => $month]);

        return array_map(
            fn(array $row) => UpcomingRelease::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
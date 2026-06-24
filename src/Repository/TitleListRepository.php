<?php
/**
 * TitleListRepository
 * Acceso a datos de la tabla title_lists, que almacena títulos
 * organizados por sección y posición (ej: 'popular').
 *
 * MÉTODOS:
 *   clearSection(section)
 *     Elimina todos los registros de una sección dada.
 *     Usado para limpiar antes de una resincronización.
 *
 *   insert(section, titleId, position)
 *     Inserta un título en una sección con su posición y timestamp de sincronización.
 *
 *   findBySection(section, limit)
 *     Retorna hasta $limit títulos de una sección, ordenados por posición.
 *     Incluye datos del título (tmdb_id, poster, año) y el promedio de score
 *     de reseñas visibles.
 *
 *   findSuggested(excludeTitleId, limit)
 *     Retorna hasta $limit títulos de la sección 'popular', excluyendo
 *     un título específico. Usado para mostrar sugerencias en el detalle
 *     de una película.
 *
 *
 * DEPENDENCIAS:
 *   PDO — conexión a la base de datos.
 */

namespace App\Repository;

use PDO;

class TitleListRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function clearSection(string $section): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM title_lists WHERE section = :section'
        );

        $stmt->execute([
            ':section' => $section,
        ]);
    }

    public function insert(string $section, int $titleId, int $position): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO title_lists (section, title_id, position, synced_at)
             VALUES (:section, :title_id, :position, NOW())'
        );

        $stmt->execute([
            ':section' => $section,
            ':title_id' => $titleId,
            ':position' => $position,
        ]);
    }

    public function findBySection(string $section, int $limit): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                t.id,
                t.tmdb_id,
                t.title,
                t.poster_url,
                t.release_year,
                COALESCE(
                    ROUND(AVG(r.score)::numeric, 1),
                    t.tmdb_vote_average
                ) AS avg_score,
                cl.position
             FROM title_lists cl
             JOIN titles t ON t.id = cl.title_id
             LEFT JOIN reviews r ON r.title_id = t.id AND r.is_visible = true
             WHERE cl.section = :section
             GROUP BY t.id, t.tmdb_id, t.title, t.poster_url, t.release_year, cl.position
             ORDER BY cl.position ASC
             LIMIT :limit'
        );

        $stmt->bindValue(':section', $section, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows ?: [];
    }

    public function findSuggested(int $excludeTitleId, int $limit): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                 t.id,
                 t.tmdb_id,
                 t.title,
                 t.poster_url
             FROM title_lists cl
             JOIN titles t ON t.id = cl.title_id
             WHERE cl.section = \'popular\'
               AND cl.title_id != :exclude_title_id
             ORDER BY cl.position ASC
             LIMIT :limit'
        );

        $stmt->bindValue(':exclude_title_id', $excludeTitleId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows ?: [];
    }

    public function findUpcomingGroupedByMonth(int $limit = 100): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                t.id,
                t.tmdb_id,
                t.title,
                t.poster_url,
                t.release_date,
                TO_CHAR(t.release_date, 'YYYY-MM') AS month
            FROM title_lists cl
            JOIN titles t ON t.id = cl.title_id
            WHERE cl.section = 'upcoming'
            AND t.release_date >= CURRENT_DATE
            ORDER BY t.release_date ASC
            LIMIT :limit"
        );

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Agrupar por mes en PHP
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['month']][] = $row;
        }

        return $grouped;
    }

    public function findUpcomingByDate(string $date): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                t.id,
                t.tmdb_id,
                t.title,
                t.poster_url,
                t.release_date
            FROM title_lists cl
            JOIN titles t ON t.id = cl.title_id
            WHERE cl.section = 'upcoming'
            AND t.release_date = :date
            ORDER BY t.title ASC"
        );

        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countUpcomingByMonth(): array
    {
        $stmt = $this->pdo->query(
            "SELECT
                TO_CHAR(t.release_date, 'YYYY-MM') AS month,
                COUNT(*) AS total
            FROM title_lists cl
            JOIN titles t ON t.id = cl.title_id
            WHERE cl.section = 'upcoming'
            AND t.release_date >= CURRENT_DATE
            GROUP BY month
            ORDER BY month ASC"
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // [ '2025-07' => 12, '2025-08' => 7, ... ]
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['month']] = (int) $row['total'];
        }

        return $counts;
    }

}

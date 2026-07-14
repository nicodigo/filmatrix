<?php

namespace App\Repository;

use PDO;

class ReviewReportRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Registra un reporte de un usuario sobre una reseña.
     *
     * @throws \PDOException con SQLSTATE[23505] si el usuario ya reportó esta reseña
     */
    public function create(int $reviewId, int $userId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO review_reports (review_id, user_id) VALUES (:review_id, :user_id)'
        );
        $stmt->execute([
            ':review_id' => $reviewId,
            ':user_id'   => $userId,
        ]);
    }

    /**
     * Cuenta cuántos reportes tiene una reseña.
     */
    public function countByReviewId(int $reviewId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM review_reports WHERE review_id = :review_id'
        );
        $stmt->execute([':review_id' => $reviewId]);
        return (int) $stmt->fetchColumn();
    }
}
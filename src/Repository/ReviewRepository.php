<?php

namespace App\Repository;

use PDO;

class ReviewRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findVisibleByTitleId(int $titleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
               r.id,
               r.score,
               r.body,
               r.created_at,
               u.username
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.title_id = :title_id
               AND r.is_visible = true
             ORDER BY r.created_at DESC'
        );

        $stmt->execute([
            ':title_id' => $titleId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows ?: [];
    }
}

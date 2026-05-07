<?php

namespace App\Repository;

use PDO;
use App\Models\Review;

class ReviewRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Review[]
     */
    public function findVisibleByTitleId(int $titleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM reviews
             WHERE title_id = :title_id
               AND is_visible = true
             ORDER BY created_at DESC'
        );

        $stmt->execute([
            ':title_id' => $titleId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            return [];
        }

        return array_map(
            fn(array $row) => Review::fromArray($row),
            $rows
        );
    }

    public function findById(int $id): ?Review
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM reviews
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

        return Review::fromArray($row);
    }

    public function findByUserAndTitle(int $userId, int $titleId): ?Review
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM reviews
             WHERE user_id = :user_id
               AND title_id = :title_id
             LIMIT 1'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':title_id' => $titleId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Review::fromArray($row);
    }

    public function save(Review $review): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reviews
                (user_id, title_id, score, body, is_flagged, is_visible, created_at, updated_at)
             VALUES
                (:user_id, :title_id, :score, :body, :is_flagged, :is_visible, NOW(), NOW())'
        );

        $stmt->execute([
            ':user_id' => $review->getUserId(),
            ':title_id' => $review->getTitleId(),
            ':score' => $review->getScore(),
            ':body' => $review->getBody(),
            ':is_flagged' => $review->isFlagged(),
            ':is_visible' => $review->isVisible(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Review $review): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE reviews
             SET
                score = :score,
                body = :body,
                is_flagged = :is_flagged,
                is_visible = :is_visible,
                updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':score' => $review->getScore(),
            ':body' => $review->getBody(),
            ':is_flagged' => $review->isFlagged(),
            ':is_visible' => $review->isVisible(),
            ':id' => $review->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM reviews
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id,
        ]);
    }
}
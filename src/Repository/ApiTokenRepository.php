<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class ApiTokenRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $userId, string $tokenHash, ?string $label): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO api_tokens (user_id, token_hash, label, created_at)
             VALUES (:user_id, :token_hash, :label, NOW())
             RETURNING id'
        );

        $stmt->execute([
            ':user_id'    => $userId,
            ':token_hash' => $tokenHash,
            ':label'      => $label,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function findActiveByHash(string $tokenHash): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, user_id FROM api_tokens
             WHERE token_hash = :token_hash AND revoked_at IS NULL'
        );

        $stmt->execute([':token_hash' => $tokenHash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function touchLastUsed(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE api_tokens SET last_used_at = NOW() WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
    }

    public function revoke(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE api_tokens SET revoked_at = NOW()
             WHERE id = :id AND user_id = :user_id AND revoked_at IS NULL'
        );
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    public function findAllByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, label, created_at, last_used_at, revoked_at
             FROM api_tokens WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php

namespace App\Repository;

use PDO;

class LoginAttemptRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function countRecentFailedAttempts(string $ip, int $windowSeconds): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
         FROM login_attempts
         WHERE ip_address = :ip
         AND successful = false
         AND attempted_at >= NOW() - make_interval(secs => :seconds)'
        );

        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':seconds', $windowSeconds, PDO::PARAM_INT);

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function record(string $ip, bool $successful): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO login_attempts
             (ip_address, successful, attempted_at)
             VALUES (:ip, :successful, NOW())'
        );

        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':successful', $successful, PDO::PARAM_BOOL);

        $stmt->execute();
    }
}

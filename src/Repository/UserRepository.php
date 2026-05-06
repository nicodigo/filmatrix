<?php

namespace App\Repository;

use PDO;
use App\Models\User;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            ':email' => trim($email)
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE username = :username LIMIT 1'
        );

        $stmt->execute([
            ':username' => trim($username)
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return User::fromArray($row);
    }

    public function save(User $user): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role, created_at, updated_at)
             VALUES (:username, :email, :password_hash, :role, NOW(), NOW())'
        );

        $stmt->execute([
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':password_hash' => $user->getPasswordHash(),
            ':role' => $user->getRole(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $username, string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET username = :username,
                 email = :email,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':username' => trim($username),
            ':email' => trim($email),
            ':id' => $id
        ]);
    }

    public function updateWithPassword(int $id, string $username, string $email, string $passwordHash): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET username = :username,
                 email = :email,
                 password_hash = :password_hash,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            ':username' => trim($username),
            ':email' => trim($email),
            ':password_hash' => $passwordHash,
            ':id' => $id
        ]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->pdo->prepare(
                'SELECT id
                 FROM users
                 WHERE email = :email
                 AND id != :id
                 LIMIT 1'
            );

            $stmt->execute([
                ':email' => trim($email),
                ':id' => $excludeId
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'SELECT id
                 FROM users
                 WHERE email = :email
                 LIMIT 1'
            );

            $stmt->execute([
                ':email' => trim($email)
            ]);
        }

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

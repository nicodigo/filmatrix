<?php

namespace App\Models;

class User
{
    private ?int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $role;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        string $username,
        string $email,
        string $passwordHash,
        string $role = 'user',
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->username = trim($username);
        $this->email = trim($email);
        $this->passwordHash = $passwordHash;
        $this->role = in_array($role, ['user', 'admin']) ? $role : 'user';
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUsername(string $username): void
    {
        $this->username = trim($username);
    }

    public function setEmail(string $email): void
    {
        $this->email = trim($email);
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function setRole(string $role): void
    {
        $this->role = in_array($role, ['user', 'admin']) ? $role : 'user';
    }


    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    public static function fromArray(array $data): User
    {
        return new self(
            isset($data['id']) ? (int)$data['id'] : null,
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'user',
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password_hash' => $this->passwordHash,
            'role' => $this->role,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
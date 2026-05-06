<?php

namespace App\Services;

use App\Repository\UserRepository;
use App\Models\User;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        return $this->userRepository->emailExists($email, $excludeId);
    }

    public function updateUser(int $id, string $username, string $email): bool
    {
        return $this->userRepository->update($id, $username, $email);
    }

    public function updateUserWithPassword(int $id, string $username, string $email, string $passwordHash): bool
    {
        return $this->userRepository->updateWithPassword($id, $username, $email, $passwordHash);
    }
}

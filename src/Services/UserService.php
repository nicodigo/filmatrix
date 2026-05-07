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

    public function updateProfile(int $id, string $username, string $email): bool
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            return false;
        }

        $user->setUsername(trim($username));
        $user->setEmail(trim($email));

        return $this->userRepository->update($user);
    }

    public function updatePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            return false;
        }

        if (!$user->verifyPassword($currentPassword)) {
            return false;
        }

        $user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));

        return $this->userRepository->update($user);
    }

    public function updateProfileWithPassword(
        int $id,
        string $username,
        string $email,
        string $currentPassword,
        string $newPassword
    ): bool {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            return false;
        }

        if (!$user->verifyPassword($currentPassword)) {
            return false;
        }

        $user->setUsername(trim($username));
        $user->setEmail(trim($email));
        $user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));

        return $this->userRepository->update($user);
    }

    public function isEmailTaken(string $email, ?int $excludeId = null): bool
    {
        $email = trim($email);
        $found = $this->userRepository->findByEmail($email);
        if ($found === null) {
            return false;
        }

        if ($excludeId !== null) {
            return $found->getId() !== $excludeId;
        }

        return true;
    }
}

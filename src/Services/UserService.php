<?php

namespace App\Services;

use App\Core\Exceptions\EmailAlreadyTakenException;
use App\Core\Exceptions\InvalidPasswordException;
use App\Core\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use App\Models\User;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(int $id): User
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            throw new UserNotFoundException("User {$id} not found");
        }
        return $user;
    }

    public function updateProfile(int $id, string $username, string $email): void
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            throw new UserNotFoundException("User {$id} not found");
        }

        $user->setUsername(trim($username));
        $user->setEmail(trim($email));

        $this->userRepository->update($user);
    }

    public function updatePassword(int $id, string $currentPassword, string $newPassword): void
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            throw new UserNotFoundException("User {$id} not found");
        }

        if (!$user->verifyPassword($currentPassword)) {
            throw new InvalidPasswordException("Current password is incorrect");
        }

        $user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));

        $this->userRepository->update($user);
    }

    public function updateProfileWithPassword(
        int $id,
        string $username,
        string $email,
        string $currentPassword,
        string $newPassword
    ): void {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            throw new UserNotFoundException("User {$id} not found");
        }

        if (!$user->verifyPassword($currentPassword)) {
            throw new InvalidPasswordException("Current password is incorrect");
        }

        $user->setUsername(trim($username));
        $user->setEmail(trim($email));
        $user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));

        $this->userRepository->update($user);
    }

    public function assertEmailNotTaken(string $email, ?int $excludeId = null): void
    {
        $email = trim($email);
        $found = $this->userRepository->findByEmail($email);
        if ($found === null) {
            return;
        }

        if ($excludeId !== null && $found->getId() === $excludeId) {
            return;
        }

        throw new EmailAlreadyTakenException("Email {$email} is already in use");
    }
}

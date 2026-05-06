<?php

namespace App\Services;

use App\Core\Exceptions\UsernameAlreadyExists;
use App\Repository\UserRepository;
use App\Models\User;
use Psr\Log\LoggerInterface;

class AuthService
{
    private UserRepository $userRepository;
    private LoggerInterface $logger;

    public function __construct(UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->logger->warning('Login attempt with unknown email', ['email' => $email]);
            return false;
        }

        if (!$user->verifyPassword($password)) {
            $this->logger->warning('Login attempt with wrong password', ['email' => $email]);
            return false;
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['user_nombre'] = $user->getUsername();

        return true;
    }

    public function register(array $data)
    {
        $existingEmail = $this->userRepository->findByEmail($data['email']);

        if ($existingEmail) {
            $this->logger->warning(
                'Registration attempt with existing email',
                ['email' => mb_strtolower($data['email'], 'UTF8')]
            );
            throw new UsernameAlreadyExists('Email registrado');
        }

        $existingUsername = $this->userRepository->findByUsername($data['username']);

        if ($existingUsername) {
            $this->logger->warning(
                'Registration attempt with existing username',
                ['username' => mb_strtolower($data['username'], 'UTF8')]
            );
            throw new UsernameAlreadyExists('Username registrado');
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = new User(
            null,
            $data['username'],
            mb_strtolower($data['email'], 'UTF8'),
            $passwordHash,
            'user'
        );

        $this->userRepository->save($user);
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public function getCurrentUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public function getCurrentUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    public function getCurrentUser(): ?User
    {
        $userId = $this->getCurrentUserId();

        if (!$userId) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }
}

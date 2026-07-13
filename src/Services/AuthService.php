<?php

namespace App\Services;

use App\Core\Exceptions\EmailAlreadyTakenException;
use App\Core\Exceptions\InvalidPasswordException;
use App\Core\Exceptions\TooManyLoginAttemptsException;
use App\Core\Exceptions\UserNotFoundException;
use App\Core\Exceptions\UsernameAlreadyExistsException;
use App\Repository\UserRepository;
use App\Models\User;
use App\Repository\LoginAttemptRepository;
use Psr\Log\LoggerInterface;

class AuthService
{
    private UserRepository $userRepository;
    private LoggerInterface $logger;
    private LoginAttemptRepository $loginAttemptRepository;
    private int $maxAttempts;
    private int $lockoutWindowSeconds;

    public function __construct(UserRepository $userRepository, LoggerInterface $logger, LoginAttemptRepository $loginAttemptRepository, int $maxAttempts, int $lockoutWindowSeconds)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->loginAttemptRepository = $loginAttemptRepository;
        $this->maxAttempts = $maxAttempts;
        $this->lockoutWindowSeconds = $lockoutWindowSeconds;
    }

    public function login(string $email, string $password, string $ip): void
    {

        $recentFailed = $this->loginAttemptRepository->countRecentFailedAttempts(
            $ip,
            $this->lockoutWindowSeconds
        );

        $this->logger->debug('failed, max: ' . $recentFailed . ' ' . $this->maxAttempts, ['ip' => $ip]);

        $this->logger->debug(
            "Checking failed attempts",
            [
                'ip' => $ip,
                'window' => $this->lockoutWindowSeconds
            ]
        );

        if ($recentFailed >= $this->maxAttempts) {
            $this->logger->warning('Login blocked by rate limit', ['ip' => $ip]);
            throw new TooManyLoginAttemptsException(
                'Demasiados intentos fallidos. Espera unos minutos e intenta nuevamente'
            );
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->loginAttemptRepository->record($ip, false);
            $this->logger->warning('Login attempt with unknown email', ['email' => $email]);
            throw new UserNotFoundException("User with email {$email} not found");
        }

        if (!$user->verifyPassword($password)) {
            $this->loginAttemptRepository->record($ip, false);
            $this->logger->warning('Login attempt with wrong password', ['email' => $email]);
            throw new InvalidPasswordException("Invalid password");
        }

        $this->loginAttemptRepository->record($ip, true);

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public function register(array $data): void
    {
        $existingEmail = $this->userRepository->findByEmail($data['email']);

        if ($existingEmail) {
            $this->logger->warning(
                'Registration attempt with existing email',
                ['email' => mb_strtolower($data['email'], 'UTF8')]
            );
            throw new EmailAlreadyTakenException('Email registrado');
        }

        $existingUsername = $this->userRepository->findByUsername($data['username']);

        if ($existingUsername) {
            $this->logger->warning(
                'Registration attempt with existing username',
                ['username' => mb_strtolower($data['username'], 'UTF8')]
            );
            throw new UsernameAlreadyExistsException('Username registrado');
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

        // Eliminar cookie (opcional pero recomendado)
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
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
        return $_SESSION['role'] ?? null;
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

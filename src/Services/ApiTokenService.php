<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exceptions\InvalidApiTokenException;
use App\Repository\ApiTokenRepository;

class ApiTokenService
{
    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function issue(int $userId, ?string $label = null): array
    {
        $plaintext = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $hash = hash('sha256', $plaintext);

        $id = $this->apiTokenRepository->create($userId, $hash, $label);

        return ['token' => $plaintext, 'id' => $id];
    }

    public function authenticate(?string $plaintext): int
    {
        if ($plaintext === null || $plaintext === '') {
            throw new InvalidApiTokenException('Falta el header Authorization: Bearer <token>');
        }

        $hash = hash('sha256', $plaintext);
        $row = $this->apiTokenRepository->findActiveByHash($hash);

        if ($row === null) {
            throw new InvalidApiTokenException();
        }

        $this->apiTokenRepository->touchLastUsed((int) $row['id']);

        return (int) $row['user_id'];
    }

    public function revoke(int $userId, int $tokenId): bool
    {
        return $this->apiTokenRepository->revoke($tokenId, $userId);
    }

    public function listForUser(int $userId): array
    {
        return $this->apiTokenRepository->findAllByUser($userId);
    }
}

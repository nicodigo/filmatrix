<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Exceptions\InvalidCredentialsException;
use App\Core\Http\ApiResponse;
use App\Core\Request;
use App\Repository\UserRepository;
use App\Services\ApiTokenService;

class AuthTokenController
{
    private ApiTokenService $apiTokenService;
    private UserRepository $userRepository;
    private Request $request;

    public function __construct(ApiTokenService $apiTokenService, UserRepository $userRepository, Request $request)
    {
        $this->apiTokenService = $apiTokenService;
        $this->userRepository  = $userRepository;
        $this->request         = $request;
    }

    // POST /api/v1/auth/tokens — no requiere Bearer, usa email+password.
    public function store(): void
    {
        $body  = $this->request->jsonBody();
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;
        $label = $body['label'] ?? null;

        if (!$email || !$password) {
            ApiResponse::error(422, 'email y password son obligatorios');
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            throw new InvalidCredentialsException();
        }

        $result = $this->apiTokenService->issue($user->getId(), $label);

        ApiResponse::json([
            'token'   => $result['token'],
            'id'      => $result['id'],
            'warning' => 'Este token se muestra una única vez. Guardalo ahora.',
        ], 201);
    }

    // GET /api/v1/auth/tokens — requiere Bearer. Lista metadata, nunca el token en texto plano.
    public function index(int $userId): void
    {
        ApiResponse::json(['data' => $this->apiTokenService->listForUser($userId)]);
    }

    // DELETE /api/v1/auth/tokens — requiere Bearer. Body: {"id": <token_id>}.
    public function destroy(int $userId): void
    {
        $body = $this->request->jsonBody();
        $tokenId = (int) ($body['id'] ?? 0);

        $revoked = $this->apiTokenService->revoke($userId, $tokenId);

        if (!$revoked) {
            ApiResponse::error(404, 'Token no encontrado');
        }

        ApiResponse::json(['revoked' => true]);
    }
}

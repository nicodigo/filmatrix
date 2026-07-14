<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Exceptions\InvalidApiTokenException;
use App\Core\Request;
use App\Services\ApiTokenService;

class ApiAuthMiddleware
{
    private ApiTokenService $apiTokenService;

    public function __construct(ApiTokenService $apiTokenService)
    {
        $this->apiTokenService = $apiTokenService;
    }

    /**
     * @throws InvalidApiTokenException
     */
    public function authenticate(Request $request): int
    {
        return $this->apiTokenService->authenticate($request->bearerToken());
    }
}

<?php

declare(strict_types=1);

namespace App\Core\Http;

class ApiResponse
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(int $status, string $message, ?string $code = null): void
    {
        self::json([
            'error' => [
                'status'  => $status,
                'code'    => $code ?? 'error',
                'message' => $message,
            ],
        ], $status);
    }
}

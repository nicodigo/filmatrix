<?php

namespace App\Middleware;

class AdminMiddleware
{
    public function handle(): void
    {
        if (($_SESSION['role'] ?? null) !== 'admin') {
            http_response_code(403);
            echo 'Acceso no autorizado ' . $_SESSION['role'];
            exit;
        }
    }
}

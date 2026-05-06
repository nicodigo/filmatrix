<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/perfil';
            header('Location: /login');
            exit;
        }
    }
}

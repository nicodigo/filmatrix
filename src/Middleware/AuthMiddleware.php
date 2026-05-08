<?php
/**
 * AuthMiddleware
 * Protege rutas que requieren autenticación.
 *
 * MÉTODOS:
 *   handle()
 *     Verifica si hay una sesión activa. Si el usuario no está autenticado,
 *     guarda la URL actual en sesión para redirigir después del login
 *     y redirige a /login.
 */
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

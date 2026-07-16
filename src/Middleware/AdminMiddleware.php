<?php

namespace App\Middleware;

class AdminMiddleware
{
    public function handle(): void
    {
        if (($_SESSION['role'] ?? null) !== 'admin') {
            // 404 en lugar de 403 — no revelar existencia de rutas admin
            http_response_code(404);
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
                . '<title>Página no encontrada — Filmatrix</title>'
                . '<meta name="robots" content="noindex">'
                . '<style>body{font-family:sans-serif;display:flex;min-height:100vh;'
                . 'align-items:center;justify-content:center;background:#0D1117;'
                . 'color:#F0F6FC;margin:0;}'
                . 'h1{font-size:3rem;margin-bottom:.5rem;}'
                . 'p{color:#8B949E;}'
                . 'a{color:#7C3AED;text-decoration:none;}'
                . 'a:hover{text-decoration:underline;}'
                . '</style></head><body>'
                . '<div style="text-align:center;">'
                . '<h1>404</h1><p>Página no encontrada</p>'
                . '<a href="/">Volver al inicio</a>'
                . '</div></body></html>';
            exit;
        }
    }
}

<?php

namespace App\Controllers;

use Twig\Environment;

class ErrorController
{
    private ?Environment $twig;

    public function __construct(?Environment $twig = null)
    {
        $this->twig = $twig;
    }

    public function notFound(): void
    {
        http_response_code(404);

        if ($this->twig !== null) {
            echo $this->twig->render('pages/error-404.html.twig');
            return;
        }

        echo 'Página no encontrada';
    }

    public function internalError(): void
    {
        http_response_code(500);

        if ($this->twig !== null) {
            echo $this->twig->render('pages/error-500.html.twig');
            return;
        }

        echo 'Error interno del servidor';
    }
}

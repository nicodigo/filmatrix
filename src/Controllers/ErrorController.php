<?php

namespace App\Controllers;

class ErrorController
{

    public string $viewsDir = __DIR__ . '/../../views';

    public function notFound()
    {
        http_response_code(404);
        echo 'pagina no encontrada';
        //TODO crear vista
        /* require $this->viewsDir . '/pages/not_found.php'; */
    }

    public function internalError()
    {
        http_response_code(500);
        echo 'Error interno';
        //TODO crear vista
        /* require $this->viewsDir . '/pages/internal_error.php'; */
    }
}

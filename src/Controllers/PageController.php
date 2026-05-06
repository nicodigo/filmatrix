<?php

namespace App\Controllers;

class PageController
{
    public string $viewsDir;

    public function __construct()
    {
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function home()
    {
        require $this->viewsDir . 'pages/home.php';
    }

    public function catalogo()
    {
        require $this->viewsDir . 'pages/catalogo.php';
    }

    public function detalle_pelicula()
    {
        require $this->viewsDir . 'pages/detalle_pelicula.php';
    }

}

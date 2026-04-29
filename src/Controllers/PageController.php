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

    public function eventos()
    {
        require $this->viewsDir . 'pages/locales.php';
    }

    public function acercaDeNosotros()
    {
        require $this->viewsDir . 'pages/nosotros.php';
    }
}

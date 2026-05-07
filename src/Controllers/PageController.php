<?php

namespace App\Controllers;

use App\Repository\CatalogListRepository;

class PageController
{
    public string $viewsDir;
    private CatalogListRepository $catalogListRepository;

    public function __construct(CatalogListRepository $catalogListRepository)
    {
        $this->viewsDir = __DIR__ . '/../../views/';
        $this->catalogListRepository = $catalogListRepository;
    }

    public function home()
    {
        $populares = $this->catalogListRepository->findBySection('popular', 4);
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

<?php

namespace App\Controllers;

use App\Repository\CatalogListRepository;

class PageController
{
    private string $viewsDir;
    private CatalogListRepository $catalogListRepository;

    public function __construct(CatalogListRepository $catalogListRepository)
    {
        $this->viewsDir = __DIR__ . '/../../views/';
        $this->catalogListRepository = $catalogListRepository;
    }

    public function home(): void
    {
        $popular = $this->catalogListRepository->findBySection('popular', 4);

        require $this->viewsDir . 'pages/home.php';
    }
}
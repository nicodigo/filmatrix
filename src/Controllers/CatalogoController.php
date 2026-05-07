<?php

namespace App\Controllers;

use App\Repository\CatalogListRepository;
use App\Services\TitleService;

class CatalogoController
{
    private TitleService $titleService;
    private CatalogListRepository $catalogListRepository;
    public string $viewsDir;

    public function __construct(TitleService $titleService, CatalogListRepository $catalogListRepository)
    {
        $this->titleService = $titleService;
        $this->catalogListRepository = $catalogListRepository;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function index(): void
    {
        $titulos = $this->catalogListRepository->findAllByPopularity(40);
        require $this->viewsDir . 'pages/catalogo.php';
    }
}

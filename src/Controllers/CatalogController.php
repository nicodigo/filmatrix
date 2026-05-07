<?php

namespace App\Controllers;

use App\Repository\CatalogListRepository;
use App\Services\TitleService;

class CatalogController
{
    private TitleService $titleService;
    private CatalogListRepository $catalogListRepository;
    private string $viewsDir;

    public function __construct(TitleService $titleService, CatalogListRepository $catalogListRepository)
    {
        $this->titleService = $titleService;
        $this->catalogListRepository = $catalogListRepository;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function index(): void
    {
        $titles = $this->catalogListRepository->findAllByPopularity(40);
        require $this->viewsDir . 'pages/catalogo.php';
    }
}

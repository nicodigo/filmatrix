<?php

namespace App\Controllers;

use App\Services\CatalogSyncService;

class CatalogController
{
    private CatalogSyncService $catalogService;
    private string $viewsDir;

    public function __construct(
        CatalogSyncService $catalogService
    ) {
        $this->catalogService = $catalogService;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function index(): void
    {
        $titles = $this->catalogService
            ->findAllByPopularity(40);

        require $this->viewsDir . 'pages/catalogo.php';
    }
}
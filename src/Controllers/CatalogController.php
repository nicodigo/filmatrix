<?php

namespace App\Controllers;

use App\Services\CatalogSyncService;
use Twig\Environment;

class CatalogController
{
    private CatalogSyncService $catalogService;
    private Environment $twig;

    public function __construct(
        Environment $twig,
        CatalogSyncService $catalogService
    ) {
        $this->catalogService = $catalogService;
        $this->twig = $twig;
    }

    public function index(): void
    {
        $titles = $this->catalogService
            ->findAllByPopularity(40);

        echo $this->twig->render('pages/catalog.html.twig', [
            'titles' => $titles,
        ]);

    }
}

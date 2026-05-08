<?php
/**
 * CatalogController
 * Maneja la visualización del catálogo general de películas.
 *
 * MÉTODOS:
 *   index()
 *     Renderiza el catálogo completo ordenado por popularidad.
 *     Obtiene hasta 40 títulos a través del CatalogSyncService.
 *     Vista: views/pages/catalogo.php
 *     Ruta: GET /catalog
 *
 * DEPENDENCIAS:
 *   CatalogSyncService — obtiene y sincroniza los títulos ordenados por popularidad.
 */

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
        $recent = $this->catalogService->findBySection('now_playing', 8);
        $popular = $this->catalogService->findBySection('popular', 8);

        echo $this->twig->render('pages/catalog.html.twig', [
            'titles' => $titles,
        ]);

    }
}

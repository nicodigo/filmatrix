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
        $recent = $this->catalogService->findBySection('now_playing', 8);
        $popular = $this->catalogService->findBySection('popular', 8);

        require $this->viewsDir . 'pages/catalogo.php';
    }
}
<?php
/**
 * PageController 
 * Maneja las páginas estáticas o de contenido general de la aplicación
 * que no pertenecen a un recurso específico (películas, usuarios, etc.).
 *
 * MÉTODOS:
 *   home()
 *     Renderiza la página principal. Obtiene las 4 películas más populares
 *     de la sección 'popular' del catálogo para mostrarlas como destacadas.
 *     Vista: views/pages/home.php
 *     Ruta: GET /
 *
 * DEPENDENCIAS:
 *   CatalogListRepository — para obtener películas destacadas del catálogo.
 *
 * NOTA: Este controller usa el repository directamente ya que es una
 * consulta de solo lectura simple que no requiere lógica de negocio.
 * 
 * (PROXIMA ACTUALIZACION: REEMPLAZAR REPOSITORY POR CatalogSyncService y que no depende del repositorio)
 */

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
<?php
/**
 * FilmController
 * Maneja la visualización del catálogo general de películas.
 *
 * MÉTODOS:
 *   index()
 *     Renderiza el catálogo completo ordenado por popularidad.
 *     Obtiene hasta 40 títulos a través del FilmSyncService.
 *     Vista: views/pages/filmo.php
 *     Ruta: GET /film
 *
 * DEPENDENCIAS:
 *   FilmSyncService — obtiene y sincroniza los títulos ordenados por popularidad.
 */

namespace App\Controllers;

use App\Core\Request;
use App\Services\FilmSyncService;
use Twig\Environment;

class FilmController
{
    private FilmSyncService $filmService;
    private Environment $twig;
    private Request $request;

    public function __construct(
        Environment $twig,
        FilmSyncService $filmService,
        Request $request
    ) {
        $this->filmService = $filmService;
        $this->twig = $twig;
        $this->request = $request;
    }

    public function index(): void
    {
        $popular = $this->filmService->findBySection('popular', 8);

        echo $this->twig->render('pages/films.html.twig', [
            'titles' => $popular,
        ]);

    }
}

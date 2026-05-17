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

use App\Services\FilmSyncService;
use Twig\Environment;

class FilmController
{
    private FilmSyncService $filmService;
    private Environment $twig;

    public function __construct(
        Environment $twig,
        FilmSyncService $filmService
    ) {
        $this->filmService = $filmService;
        $this->twig = $twig;
    }

    public function index(): void
    {
        $recent = $this->filmService->findBySection('now_playing', 8);
        $popular = $this->filmService->findBySection('popular', 8);

        echo $this->twig->render('pages/films.html.twig', [
            'titles' => $popular,
        ]);

    }
}

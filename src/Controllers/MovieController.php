<?php
/**
 * MovieController
 * Maneja la visualización del detalle de una película.
 *
 * MÉTODOS:
 *   show(): Renderiza la página de detalle de una película a partir de su tmdb_id
 *           recibido por query string. Si el ID es inválido o la película no existe,
 *           redirige a /catalog.
 *
 *     Datos que resuelve antes de renderizar:
 *       - Géneros del título (formateados como string para la vista).
 *       - Elenco principal.
 *       - Reseñas visibles.
 *       - 4 películas sugeridas relacionadas.
 *       - Duración formateada en horas y minutos (ej: "2h 15m").
 *
 *     Vista: views/pages/detalle_pelicula.php
 *     Ruta: GET /movie?tmdb_id={id}
 *
 * DEPENDENCIAS:
 *   TitleService       — obtiene los datos principales del título.
 *   GenreService       — obtiene los géneros asociados al título.
 *   PeopleService      — obtiene el elenco del título.
 *   ReviewService      — obtiene las reseñas visibles del título.
 *   CatalogSyncService — obtiene películas sugeridas relacionadas.
 */

namespace App\Controllers;

use App\Services\CatalogSyncService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Services\GenreService;
use App\Services\PeopleService;

class MovieController
{
    private TitleService $titleService;
    private ReviewService $reviewService;
    private CatalogSyncService $catalogSyncService;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private string $viewsDir;

    public function __construct(
        TitleService $titleService,
        ReviewService $reviewService,
        CatalogSyncService $catalogSyncService,
        GenreService $genreService,
        PeopleService $peopleService
    ) {
        $this->titleService = $titleService;
        $this->reviewService = $reviewService;
        $this->catalogSyncService = $catalogSyncService;
        $this->genreService = $genreService;
        $this->peopleService = $peopleService;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function show(): void
    {
        $tmdbId = (int) ($_GET['tmdb_id'] ?? 0);

        if ($tmdbId === 0) {
            header('Location: /catalog');
            exit;
        }

        $title = $this->titleService->getTitle($tmdbId);

        if ($title === null) {
            header('Location: /catalog');
            exit;
        }

        /*
        | Relaciones desde services
        */
        $genres = $this->genreService->getByTitleId($title->getId());
        $cast   = $this->peopleService->getCastByTitleId($title->getId());

        $reviews = $this->reviewService
            ->getByTitleIdWithAuthor($title->getId());

        $suggested = $this->catalogSyncService
            ->findSuggested($title->getId(), 4);

        /*
        | Duración formateada
        */
        $duration = null;

        if ($title->getDurationMinutes() !== null) {
            $hours = (int) floor($title->getDurationMinutes() / 60);
            $mins  = (int) ($title->getDurationMinutes() % 60);

            $duration =
                ($hours > 0 ? $hours . 'h ' : '') .
                ($mins  > 0 ? $mins  . 'm'  : '');
        }

        /*
        | Labels para vista
        */
        $genreLabel = implode(', ', array_map(
            fn($genre) => is_array($genre) ? $genre['name'] : $genre->getName(),
            $genres
        ));

        /*
        | Render
        */
        require $this->viewsDir . 'pages/detalle_pelicula.php';
    }
}

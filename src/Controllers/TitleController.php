<?php
/**
 * TitleController
 * Maneja el catálogo general y el detalle de títulos (películas/series).
 *
 * MÉTODOS:
 *   index()
 *     Renderiza el catálogo de títulos.
 *     FUNCIONAMIENTO:
 *       - Si existe un parámetro de búsqueda `q`, realiza una búsqueda
 *         de títulos mediante TitleService.
 *       - Si no hay búsqueda, obtiene los títulos de la sección
 *         "popular" usando TitleListService.
 *
 *     DATOS ENVIADOS A LA VISTA:
 *       - titles        → listado de títulos a mostrar.
 *       - search_query  → texto buscado por el usuario.
 *
 *     Vista: views/pages/titles.html.twig
 *     Ruta: GET /titles
 *
 *   show()
 *     Renderiza la página de detalle de un título a partir de su tmdb_id
 *     recibido por query string. Si el ID es inválido o el título no existe,
 *     redirige a /titles.
 *     Vista: views/pages/title-detail.html.twig
 *     Ruta: GET /titles?tmdb_id={id}
 *
 * DEPENDENCIAS:
 *   TitleService      — datos principales del título.
 *   ReviewService     — reseñas del título.
 *   GenreService      — géneros asociados al título.
 *   PeopleService     — elenco del título.
 */

namespace App\Controllers;

use App\Core\Request;
use App\Services\TitleService;
use App\Services\TitleListService;
use App\Services\ReviewService;
use App\Services\GenreService;
use App\Services\PeopleService;
use Twig\Environment;

class TitleController
{
    private Environment $twig;
    private TitleListService $titleListService;
    private TitleService $titleService;
    private ReviewService $reviewService;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private Request $request;

    public function __construct(
        Environment $twig,
        TitleListService $titleListService,
        TitleService $titleService,
        ReviewService $reviewService,
        GenreService $genreService,
        PeopleService $peopleService,
        Request $request
    ) {
        $this->twig = $twig;
        $this->titleListService = $titleListService;
        $this->titleService = $titleService;
        $this->reviewService = $reviewService;
        $this->genreService = $genreService;
        $this->peopleService = $peopleService;
        $this->request = $request;
    }

    public function index(): void
    {
        $query = trim($this->request->get('q', ''));

        if ($query !== '') {
            $titles = array_map(
                fn($t) => $t->toArray(),
                $this->titleService->search($query)
            );
        } else {
            $titles = $this->titleListService->findBySection('popular', 8);
        }

        echo $this->twig->render('pages/titles.html.twig', [
            'titles'       => $titles,
            'search_query' => $query,
        ]);
    }

    public function show(): void
    {
        $tmdbId = (int) ($this->request->get('tmdb_id', 0));

        if ($tmdbId === 0) {
            header('Location: /titles');
            exit;
        }

        $title = $this->titleService->getTitle($tmdbId);

        if ($title === null) {
            header('Location: /titles');
            exit;
        }

        $genres = $this->genreService->getByTitleId($title->getId());
        $cast   = $this->peopleService->getCastByTitleId($title->getId());

        $reviews = $this->reviewService
            ->getByTitleIdWithAuthor($title->getId());

        $suggested = $this->titleListService
            ->findSuggested($title->getId(), 4);

        $duration = null;

        if ($title->getDurationMinutes() !== null) {
            $hours = (int) floor($title->getDurationMinutes() / 60);
            $mins  = (int) ($title->getDurationMinutes() % 60);

            $duration =
                ($hours > 0 ? $hours . 'h ' : '') .
                ($mins  > 0 ? $mins  . 'm'  : '');
        }

        $genreLabel = implode(', ', array_map(
            fn($genre) => is_array($genre) ? $genre['name'] : $genre->getName(),
            $genres
        ));

        $flashError = $this->request->getFlash('error');

        echo $this->twig->render('pages/title-detail.html.twig', [
            'title' => $title,
            'genres' => $genres,
            'genreLabel' => $genreLabel,
            'cast' => $cast,
            'reviews' => $reviews,
            'suggested' => $suggested,
            'duration' => $duration,
            'tmdbId' => $tmdbId,
            'flashError' => $flashError,
        ]);
    }
}

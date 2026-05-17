<?php
/**
 * MovieController
 * Maneja la visualización del detalle de una película.
 *
 * MÉTODOS:
 *   show(): Renderiza la página de detalle de una película a partir de su tmdb_id
 *           recibido por query string. Si el ID es inválido o la película no existe,
 *           redirige a /films.
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
 *   FilmSyncService — obtiene películas sugeridas relacionadas.
 */

namespace App\Controllers;

use App\Core\Request;
use App\Services\FilmSyncService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Services\GenreService;
use App\Services\PeopleService;
use Twig\Environment;

class MovieController
{
    private Environment $twig;
    private TitleService $titleService;
    private ReviewService $reviewService;
    private FilmSyncService $filmSyncService;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private Request $request;

    public function __construct(
        Environment $twig,
        TitleService $titleService,
        ReviewService $reviewService,
        FilmSyncService $filmSyncService,
        GenreService $genreService,
        PeopleService $peopleService,
        Request $request
    ) {
        $this->twig = $twig;
        $this->titleService = $titleService;
        $this->reviewService = $reviewService;
        $this->filmSyncService = $filmSyncService;
        $this->genreService = $genreService;
        $this->peopleService = $peopleService;
        $this->request = $request;
    }

    public function showMovie(): void
    {
        $tmdbId = (int) ($this->request->get('tmdb_id', 0));

        if ($tmdbId === 0) {
            header('Location: /films');
            exit;
        }

        $title = $this->titleService->getTitle($tmdbId);

        if ($title === null) {
            header('Location: /films');
            exit;
        }

        /*
        | Relaciones desde services
        */
        $genres = $this->genreService->getByTitleId($title->getId());
        $cast   = $this->peopleService->getCastByTitleId($title->getId());

        $reviews = $this->reviewService
            ->getByTitleIdWithAuthor($title->getId());

        $suggested = $this->filmSyncService
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

        $flashError = $this->request->getFlash('error');

        /*
        | Render
        */
        echo $this->twig->render('pages/movieDetails.html.twig', [
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

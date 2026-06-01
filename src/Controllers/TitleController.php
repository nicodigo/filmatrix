<?php
/**
 * TitleController
 * Maneja el catálogo general y el detalle de títulos (películas/series).
 *
 * MÉTODOS:
 *   index()
 *     Renderiza el catálogo de títulos.
 *
 *     FUNCIONAMIENTO:
 *       - Si existe un parámetro de búsqueda `q`, realiza una búsqueda
 *         textual de títulos mediante TitleService.
 *
 *       - Si no hay búsqueda pero existen filtros activos, aplica filtros
 *         combinables por:
 *           · género
 *           · año de estreno
 *           · idioma
 *           · score mínimo
 *
 *       - Si no hay búsqueda ni filtros, consulta TMDB Discover sin filtros
 *         (devuelve películas populares por defecto).
 *
 *     PARÁMETROS QUERY SOPORTADOS:
 *       - q         → texto de búsqueda.
 *       - genre    → ID de género.
 *       - year     → año de estreno.
 *       - language → idioma original.
 *       - score    → score promedio mínimo.
 *
 *     DATOS ENVIADOS A LA VISTA:
 *       - titles          → listado de títulos a mostrar.
 *       - search_query    → texto buscado por el usuario.
 *       - genres          → listado de géneros disponibles.
 *       - active_filters  → filtros actualmente seleccionados.
 *
 *     Vista: views/pages/titles.html.twig
 *     Ruta: GET /titles
 *
 *    show()
 *     Renderiza la página de detalle de un título a partir de su tmdb_id
 *     recibido por query string.
 *
 *     VALIDACIONES:
 *       - Si el tmdb_id es inválido o igual a 0, redirige a /titles.
 *       - Si el título no existe, redirige a /titles.
 *
 *     DATOS OBTENIDOS:
 *       - Información principal del título.
 *       - Géneros asociados.
 *       - Elenco principal.
 *       - Reseñas visibles con autor.
 *       - Títulos sugeridos relacionados.
 *       - Reseña del usuario autenticado (si existe).
 *
 *     PROCESAMIENTO:
 *       - Convierte la duración en minutos a formato legible
 *         (ej: 2h 15m).
 *       - Genera una etiqueta de géneros concatenada para la vista.
 *       - Recupera mensajes flash de éxito y error desde sesión.
 *
 *     DATOS ENVIADOS A LA VISTA:
 *       - title        → título principal.
 *       - genres       → géneros asociados.
 *       - genreLabel   → géneros concatenados en texto.
 *       - cast         → elenco del título.
 *       - reviews      → reseñas visibles.
 *       - suggested    → títulos sugeridos.
 *       - duration     → duración formateada.
 *       - tmdbId       → identificador TMDB.
 *       - userReview   → reseña del usuario autenticado.
 *       - flashError   → mensaje flash de error.
 *       - flashSuccess → mensaje flash de éxito.
 *
 *     Vista: views/pages/title-detail.html.twig
 *     Ruta: GET /titles/detail?tmdb_id={id}
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
use App\Models\TitleCardDto;
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
        $query    = trim($this->request->get('q', ''));
        $genreId  = $this->request->get('genre')    ? (int)   $this->request->get('genre')    : null;
        $year     = $this->request->get('year')     ? (int)   $this->request->get('year')     : null;
        $language = $this->request->get('language') ? (string)$this->request->get('language') : null;
        $minScore = $this->request->get('score')    ? (float) $this->request->get('score')    : null;

        $hasFilters = $genreId || $year || $language || $minScore;

        if ($query !== '') {
            $titles = array_map(
                fn($t) => TitleCardDto::fromTitle($t),
                $this->titleService->search($query)
            );
        } elseif ($hasFilters) {
            if ($minScore !== null) {
                $titles = array_map(
                    fn($t) => TitleCardDto::fromTitle($t),
                    $this->titleService->filter($genreId, $year, $language, $minScore)
                );
            } else {
                $titles = array_map(
                    fn($t) => TitleCardDto::fromTitle($t),
                    $this->titleService->discover($genreId, $year, $language)
                );
            }
        } else {
            $titles = array_map(
                fn($t) => TitleCardDto::fromTitle($t),
                $this->titleService->discover(null, null, null)
            );
        }

        $genres = $this->genreService->getAll();

        echo $this->twig->render('pages/titles.html.twig', [
            'titles'         => $titles,
            'search_query'   => $query,
            'genres'         => $genres,
            'active_filters' => [
                'genre'    => $genreId,
                'year'     => $year,
                'language' => $language,
                'score'    => $minScore,
            ],
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

        $userReview = null;
        if (!empty($_SESSION['user_id'])) {
            $userReview = $this->reviewService
                ->getByUserAndTitle((int) $_SESSION['user_id'], $title->getId());
        }

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

        $flashError   = $this->request->getFlash('error');
        $flashSuccess = $this->request->getFlash('success');

        echo $this->twig->render('pages/title-detail.html.twig', [
            'title'        => $title,
            'genres'       => $genres,
            'genreLabel'   => $genreLabel,
            'cast'         => $cast,
            'reviews'      => $reviews,
            'suggested'    => $suggested,
            'duration'     => $duration,
            'tmdbId'       => $tmdbId,
            'userReview'   => $userReview,
            'flashError'   => $flashError,
            'flashSuccess' => $flashSuccess,
        ]);
    }
}

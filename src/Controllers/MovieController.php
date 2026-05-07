<?php

namespace App\Controllers;

use App\Services\CatalogSyncService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Repository\GenreRepository;
use App\Repository\PeopleRepository;

class MovieController
{
    private TitleService $titleService;
    private ReviewService $reviewService;
    private CatalogSyncService $catalogSyncService;
    private GenreRepository $genreRepository;
    private PeopleRepository $peopleRepository;
    private string $viewsDir;

    public function __construct(
        TitleService $titleService,
        ReviewService $reviewService,
        CatalogSyncService $catalogSyncService,
        GenreRepository $genreRepository,
        PeopleRepository $peopleRepository
    ) {
        $this->titleService = $titleService;
        $this->reviewService = $reviewService;
        $this->catalogSyncService = $catalogSyncService;
        $this->genreRepository = $genreRepository;
        $this->peopleRepository = $peopleRepository;
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
        |--------------------------------------------------------------------------
        | Relaciones reales desde BD
        |--------------------------------------------------------------------------
        */
        $genres = $this->titleService->getTitleGenres($title->getId()) ?? [];
        $cast   = $this->titleService->getTitleCast($title->getId()) ?? [];

        $reviews = $this->reviewService
            ->getVisibleByTitleId($title->getId());

        $suggested = $this->catalogSyncService
            ->findSuggested($title->getId(), 4);

        /*
        |--------------------------------------------------------------------------
        | Duración formateada
        |--------------------------------------------------------------------------
        */
        $duration = null;

        if ($title->getDurationMinutes() !== null) {
            $hours = (int) floor($title->getDurationMinutes() / 60);
            $mins = (int) ($title->getDurationMinutes() % 60);

            $duration =
                ($hours > 0 ? $hours . 'h ' : '') .
                ($mins > 0 ? $mins . 'm' : '');
        }

        /*
        |--------------------------------------------------------------------------
        | Labels para vista
        |--------------------------------------------------------------------------
        */
        $genreLabel = implode(', ', array_map(
            fn($genre) => is_array($genre) ? $genre['name'] : $genre->getName(),
            $genres
        ));

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */
        require $this->viewsDir . 'pages/detalle_pelicula.php';
    }
}
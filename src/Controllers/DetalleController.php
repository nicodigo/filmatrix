<?php

namespace App\Controllers;

use App\Repository\CatalogListRepository;
use App\Repository\ReviewRepository;
use App\Repository\TitleRepository;
use App\Services\TitleService;

class DetalleController
{
    private TitleService $titleService;
    private TitleRepository $titleRepository;
    private ReviewRepository $reviewRepository;
    private CatalogListRepository $catalogListRepository;
    public string $viewsDir;

    public function __construct(
        TitleService $titleService,
        TitleRepository $titleRepository,
        ReviewRepository $reviewRepository,
        CatalogListRepository $catalogListRepository
    ) {
        $this->titleService = $titleService;
        $this->titleRepository = $titleRepository;
        $this->reviewRepository = $reviewRepository;
        $this->catalogListRepository = $catalogListRepository;
        $this->viewsDir = __DIR__ . '/../../views/';
    }

    public function show(): void
    {
        $tmdbId = (int) ($_GET['tmdb_id'] ?? 0);
        if ($tmdbId === 0) {
            header('Location: /catalogo');
            exit;
        }

        $title = $this->titleService->getTitle($tmdbId);
        if ($title === null) {
            header('Location: /catalogo');
            exit;
        }

        $genres    = $this->titleRepository->findGenresByTitleId($title['id']);
        $cast      = $this->titleRepository->findCastByTitleId($title['id']);
        $reviews   = $this->reviewRepository->findVisibleByTitleId($title['id']);
        $suggested = $this->catalogListRepository->findSuggested($title['id'], 4);

        $duracion = null;
        if ($title['duration_minutes'] !== null) {
            $hours = (int) floor($title['duration_minutes'] / 60);
            $mins  = (int) ($title['duration_minutes'] % 60);
            $duracion = ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
        }

        $generoLabel = implode(', ', array_column($genres, 'name'));

        require $this->viewsDir . 'pages/detalle_pelicula.php';
    }
}

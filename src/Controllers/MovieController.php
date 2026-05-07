<?php

namespace App\Controllers;

use App\Repository\ReviewRepository;
use App\Services\CatalogSyncService;
use App\Services\TitleService;

class MovieController
{
    private TitleService $titleService;
    private ReviewRepository $reviewRepository;
    private CatalogSyncService $catalogSyncService;
    private string $viewsDir;

    public function __construct(
        TitleService $titleService,
        ReviewRepository $reviewRepository,
        CatalogSyncService $catalogSyncService
    ) {
        $this->titleService = $titleService;
        $this->reviewRepository = $reviewRepository;
        $this->catalogSyncService = $catalogSyncService;
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

        $genres    = $this->titleService->getTitleGenres($title['id']);
        $cast      = $this->titleService->getTitleCast($title['id']);
        $reviews   = $this->reviewRepository->findVisibleByTitleId($title['id']);
        $suggested = $this->catalogSyncService->findSuggested($title['id'], 4);

        $duration = null;
        if ($title['duration_minutes'] !== null) {
            $hours = (int) floor($title['duration_minutes'] / 60);
            $mins  = (int) ($title['duration_minutes'] % 60);
            $duration = ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
        }

        $genreLabel = implode(', ', array_column($genres, 'name'));

        require $this->viewsDir . 'pages/detalle_pelicula.php';
    }
}

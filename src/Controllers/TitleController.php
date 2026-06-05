<?php

namespace App\Controllers;

use App\Core\Request;
use App\Dtos\CatalogQuery;
use App\Services\TitleService;
use App\Services\TitleListService;
use App\Services\ReviewService;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\WatchlistService;
use Twig\Environment;

class TitleController
{
    private Environment $twig;
    private TitleListService $titleListService;
    private TitleService $titleService;
    private ReviewService $reviewService;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private WatchlistService $watchlistService;
    private Request $request;

    public function __construct(
        Environment $twig,
        TitleListService $titleListService,
        TitleService $titleService,
        ReviewService $reviewService,
        GenreService $genreService,
        PeopleService $peopleService,
        WatchlistService $watchlistService,
        Request $request
    ) {
        $this->twig = $twig;
        $this->titleListService = $titleListService;
        $this->titleService = $titleService;
        $this->reviewService = $reviewService;
        $this->genreService = $genreService;
        $this->peopleService = $peopleService;
        $this->watchlistService = $watchlistService;
        $this->request = $request;
    }

    public function index(): void
    {
        $query = new CatalogQuery(
            genreId: $this->request->get('genre')    ? (int)   $this->request->get('genre')    : null,
            year: $this->request->get('year')      ? (int)   $this->request->get('year')     : null,
            language: $this->request->get('language')  ? (string)$this->request->get('language') : null,
            minScore: $this->request->get('score')     ? (float) $this->request->get('score')    : null,
            page: max(1, (int) $this->request->get('page', 1)),
            sort: $this->request->get('sort', 'release_year'),
        );

        $result = $this->titleService->getCatalog($query);
        $genres = $this->genreService->getAll();

        $baseParams = array_filter([
            'genre'    => $query->genreId,
            'year'     => $query->year,
            'language' => $query->language,
            'score'    => $query->minScore,
            'sort'     => $query->sort,
        ], fn($v) => $v !== null);

        $prevUrl = null;
        $nextUrl = null;

        if ($result->hasPrevPage()) {
            $prevUrl = '/titles?' . http_build_query($baseParams + ['page' => $result->currentPage - 1]);
        }

        if ($result->hasNextPage()) {
            $nextUrl = '/titles?' . http_build_query($baseParams + ['page' => $result->currentPage + 1]);
        }

        echo $this->twig->render('pages/titles.html.twig', [
            'titles'         => $result->items,
            'pagination'     => $result,
            'genres'         => $genres,
            'active_filters' => $baseParams,
            'prevUrl'        => $prevUrl,
            'nextUrl'        => $nextUrl,
        ]);
    }

    public function search(): void
    {
        $query = trim($this->request->get('q', ''));

        if ($query === '') {
            header('Location: /titles');
            exit;
        }

        $page = max(1, (int) $this->request->get('page', 1));
        $result = $this->titleService->search($query, $page);

        echo $this->twig->render('pages/titles.html.twig', [
            'titles'       => $result->items,
            'pagination'   => $result,
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

        $watchlistItem = null;
        $userId = $this->request->session('user_id');
        if ($userId !== null) {
            $watchlistItem = $this->watchlistService->getItem(
                $userId,
                $title->getId()
            );
        }

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
            'watchlistItem' => $watchlistItem,
        ]);
    }
}

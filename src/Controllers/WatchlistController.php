<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\WatchlistService;
use RuntimeException;
use Twig\Environment;

class WatchlistController
{
    private Environment $twig;
    private WatchlistService $watchlistService;
    private Request $request;
    private int $perPage;

    public function __construct(WatchlistService $watchlistService, Environment $twig, Request $request)
    {
        $this->watchlistService = $watchlistService;
        $this->twig = $twig;
        $this->request = $request;
        $this->perPage = 20;
    }

    // GET /watchlist?status=pending
    public function index(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist index");
        }

        $status = $this->request->get('status');
        $page = max(1, $this->request->get('page', 1));
        $offset = ($page - 1) * $this->perPage;

        $items = $this->watchlistService->getUserWatchlistPaginated($userId, $this->perPage, $offset, $status);
        $totalWatchItems = $this->watchlistService->lengthUserWatchlist($userId, $status);
        $totalPages = (int) ceil($totalWatchItems / $this->perPage);

        $this->twig->display(
            'pages/watchlist.html.twig',
            [
                'items' => $items,
                'status' => $status,
                'page' => $page,
                'total_pages' => $totalPages,
            ]
        );
    }
}

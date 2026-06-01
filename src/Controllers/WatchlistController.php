<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Models\TitleCardDto;
use App\Services\WatchlistService;
use Exception;
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

    // GET /my-watchlist?status=pending
    public function index(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist index");
        }

        try {
            $status = $this->request->get('status');
            $page = max(1, $this->request->get('page', 1));
            $offset = ($page - 1) * $this->perPage;

            $items = $this->watchlistService->getUserWatchlistPaginated($userId, $this->perPage, $offset, $status);
            $totalWatchItems = $this->watchlistService->lengthUserWatchlist($userId, $status);
            $totalPages = (int) ceil($totalWatchItems / $this->perPage);

            $mappedItems = array_map(fn($entry) => TitleCardDto::fromWatchlistEntry($entry), $items);

            $this->twig->display(
                'pages/watchlist.html.twig',
                [
                    'items' => $mappedItems,
                    'status' => $status,
                    'page' => $page,
                    'total_pages' => $totalPages,
                ]
            );
        } catch (Exception) {
            $this->twig->display('pages/error-500.html.twig');
        }
    }

    // post /my-watchlist/store
    public function store(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist index");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $tmdbId = (int) ($body['tmdb_id'] ?? 0);
        $status = $body['status'] ?? 'pending';
        header('Content-Type: application/json');

        try {
            $this->watchlistService->addTitle($userId, $tmdbId, $status);
            echo json_encode(['success' => true]);
        } catch (Exception) {
            echo json_encode(['success' => false]);
        }
    }
}

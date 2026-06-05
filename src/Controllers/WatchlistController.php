<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Dtos\TitleCardDto;
use App\Dtos\WatchlistQuery;
use App\Services\WatchlistService;
use Exception;
use RuntimeException;
use Twig\Environment;

class WatchlistController
{
    private Environment $twig;
    private WatchlistService $watchlistService;
    private Request $request;

    public function __construct(WatchlistService $watchlistService, Environment $twig, Request $request)
    {
        $this->watchlistService = $watchlistService;
        $this->twig = $twig;
        $this->request = $request;
    }

    // GET /my-watchlist?status=pending&page=1
    public function index(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist index");
        }

        $query = new WatchlistQuery(
            status: $this->request->get('status'),
            page:   max(1, (int) $this->request->get('page', 1)),
        );

        $result = $this->watchlistService->getPaginated($userId, $query);

        $mappedItems = array_map(
            fn($entry) => TitleCardDto::fromWatchlistEntry($entry),
            $result->items,
        );

        $baseParams = array_filter([
            'status' => $query->status,
        ], fn($v) => $v !== null);

        $prevUrl = null;
        $nextUrl = null;

        if ($result->hasPrevPage()) {
            $prevUrl = '/my-watchlist?' . http_build_query($baseParams + ['page' => $result->currentPage - 1]);
        }

        if ($result->hasNextPage()) {
            $nextUrl = '/my-watchlist?' . http_build_query($baseParams + ['page' => $result->currentPage + 1]);
        }

        $this->twig->display('pages/watchlist.html.twig', [
            'items'      => $mappedItems,
            'pagination' => $result,
            'prevUrl'    => $prevUrl,
            'nextUrl'    => $nextUrl,
        ]);
    }

    // post /my-watchlist
    public function store(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist post");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $titleId = (int) ($body['title_id'] ?? 0);
        $status = $body['status'] ?? 'pending';
        header('Content-Type: application/json');

        try {
            $this->watchlistService->addTitle($userId, $titleId, $status);
            echo json_encode(['success' => true]);
        } catch (Exception) {
            echo json_encode(['success' => false]);
        }
    }

    // put /my-watchlist
    public function update(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist update");
        }

        $body = json_decode(file_get_contents('php://input'), true);

        $status = $body['status'] ?? null;
        if ($status === null) {
            throw new RuntimeException("El campo status es obligatorio");
        }

        $titleId = (int) ($body['title_id'] ?? 0);
        header('Content-Type: application/json');

        try {
            $this->watchlistService->updateStatus($userId, $titleId, $status);
            echo json_encode(['success' => true]);
        } catch (Exception) {
            echo json_encode(['success' => false]);
        }
    }

    // delete /my-watchlist
    public function delete(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on watchlist delete");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $titleId = (int) ($body['title_id'] ?? 0);
        header('Content-Type: application/json');

        try {
            $this->watchlistService->deleteItem($userId, $titleId);
            echo json_encode(['success' => true]);
        } catch (Exception) {
            echo json_encode(['success' => false]);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Exceptions\WatchlistItemAlreadyExistsException;
use App\Core\Exceptions\WatchlistItemNotFoundException;
use App\Core\Http\ApiResponse;
use App\Core\Request;
use App\Dtos\WatchlistResource;
use App\Services\WatchlistService;
use Exception;
use InvalidArgumentException;

class WatchlistApiController
{
    private WatchlistService $watchlistService;
    private Request $request;

    public function __construct(WatchlistService $watchlistService, Request $request)
    {
        $this->watchlistService = $watchlistService;
        $this->request          = $request;
    }

    // GET /api/v1/watchlist?status=pending (status opcional)
    public function index(int $userId): void
    {
        $items = $this->watchlistService->getUserWatchlist($userId, $this->request->get('status'));
        ApiResponse::json(WatchlistResource::collection($items));
    }

    // GET /api/v1/watchlist?title_id=
    public function show(int $userId): void
    {
        $titleId = (int) $this->request->get('title_id', 0);
        $item = $this->watchlistService->getItem($userId, $titleId);

        if (!$item) {
            ApiResponse::error(404, 'No está en la watchlist');
        }

        ApiResponse::json(WatchlistResource::fromItem($item));
    }

    // POST /api/v1/watchlist  body: {title_id, status?}
    public function store(int $userId): void
    {
        $body = $this->request->jsonBody();

        try {
            $item = $this->watchlistService->addTitle(
                $userId,
                (int) ($body['title_id'] ?? 0),
                $body['status'] ?? 'pending'
            );
        } catch (WatchlistItemAlreadyExistsException $e) {
            ApiResponse::error(409, $e->getMessage());
        } catch (InvalidArgumentException $e) {
            ApiResponse::error(422, $e->getMessage());
        } catch (Exception){
            ApiResponse::error(500, "Error interno");
        }

        ApiResponse::json(WatchlistResource::fromItem($item), 201);
    }

    // PATCH /api/v1/watchlist  body: {title_id, status}
    public function update(int $userId): void
    {
        $body = $this->request->jsonBody();

        try {
            $item = $this->watchlistService->updateStatus(
                $userId,
                (int) ($body['title_id'] ?? 0),
                $body['status'] ?? ''
            );
        } catch (WatchlistItemNotFoundException $e) {
            ApiResponse::error(404, $e->getMessage());
        } catch (InvalidArgumentException $e) {
            ApiResponse::error(422, $e->getMessage());
        } catch (Exception){
            ApiResponse::error(500, "Error interno");
        }

        ApiResponse::json(WatchlistResource::fromItem($item));
    }

    // DELETE /api/v1/watchlist  body: {title_id}
    public function destroy(int $userId): void
    {
        $body = $this->request->jsonBody();
        $this->watchlistService->deleteItem($userId, (int) ($body['title_id'] ?? 0));

        ApiResponse::json(['deleted' => true]);
    }
}

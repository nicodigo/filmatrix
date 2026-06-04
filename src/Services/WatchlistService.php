<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exceptions\WatchlistItemAlreadyExistsException;
use App\Core\Exceptions\WatchlistItemNotFoundException;
use App\Models\WatchlistItem;
use App\Repository\WatchlistRepository;
use InvalidArgumentException;

class WatchlistService
{
    private const VALID_STATUSES = ['pending', 'watched'];
    private WatchlistRepository $watchlistRepository;
    private TitleService $titleService;

    public function __construct(WatchlistRepository $watchlistRepository, TitleService $titleService)
    {
        $this->watchlistRepository = $watchlistRepository;
        $this->titleService = $titleService;
    }

    private function assertValidStatus(?string $status)
    {
        if ($status === null || !in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("Invalid status: $status");
        }
    }

    public function getUserWatchlist(int $userId, ?string $status = null): array
    {
        if ($status !== null) {
            $this->assertValidStatus($status);
        }
        return $this->watchlistRepository->findByUser($userId, $status);
    }

    public function getUserWatchlistPaginated(int $userId, int $limit, int $offset, ?string $status = null): array
    {
        if ($status !== null) {
            $this->assertValidStatus($status);
        }
        return $this->watchlistRepository->findByUserPaginated($userId, $limit, $offset, $status);
    }

    public function getItem(int $userId, int $titleId): ?WatchlistItem
    {
        return $this->watchlistRepository->findByUserAndTitle($userId, $titleId);
    }

    public function addTitle(int $userId, int $titleId, string $status = 'pending'): WatchlistItem
    {
        if ($status !== null) {
            $this->assertValidStatus($status);
        }

        $existing = $this->watchlistRepository->findByUserAndTitle($userId, $titleId);

        if ($existing !== null) {
            throw new WatchlistItemAlreadyExistsException("Title $titleId already exists in watchlis");
        }

        return $this->watchlistRepository->insert($userId, $titleId, $status);
    }

    public function updateStatus(int $userId, int $titleId, string $status): WatchlistItem
    {
        $this->assertValidStatus($status);

        $item = $this->watchlistRepository->findByUserAndTitle($userId, $titleId);

        if ($item === null) {
            throw new WatchlistItemNotFoundException();
        }

        $this->watchlistRepository->updateStatus($userId, $titleId, $status);

        return $this->watchlistRepository->findByUserAndTitle($userId, $titleId);
    }

    public function deleteItem(int $userId, int $titleId): bool
    {
        return $this->watchlistRepository->delete($userId, $titleId);
    }

    public function lengthUserWatchlist(int $userId, ?string $status = null): int
    {
        if ($status !== null) {
            $this->assertValidStatus($status);
        }

        return $this->watchlistRepository->countUserWatchlistItems($userId, $status);
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exceptions\WatchlistItemAlreadyExistsException;
use App\Core\Exceptions\WatchlistItemNotFoundException;
use App\Dtos\WatchlistQuery;
use App\Dtos\WatchlistResult;
use App\Models\WatchlistItem;
use App\Repository\WatchlistRepository;
use InvalidArgumentException;

class WatchlistService
{
    private const VALID_STATUSES = ['pending', 'watched'];
    private const int PER_PAGE = 40;
    private WatchlistRepository $watchlistRepository;
    private TitleService $titleService;
    private ?GenrePreferenceService $preferenceService;

    public function __construct(
        WatchlistRepository    $watchlistRepository,
        TitleService           $titleService,
        ?GenrePreferenceService $preferenceService = null,
    ) {
        $this->watchlistRepository = $watchlistRepository;
        $this->titleService        = $titleService;
        $this->preferenceService   = $preferenceService;
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

    public function getPaginated(int $userId, WatchlistQuery $query): WatchlistResult
    {
        if ($query->status !== null) {
            $this->assertValidStatus($query->status);
        }

        $offset = ($query->page - 1) * self::PER_PAGE;

        $items = $this->watchlistRepository->findByUserPaginated(
            $userId,
            self::PER_PAGE,
            $offset,
            $query->status,
        );

        $total      = $this->watchlistRepository->countUserWatchlistItems($userId, $query->status);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));

        return new WatchlistResult($items, $query->page, $totalPages, $query->status);
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

        $item = $this->watchlistRepository->insert($userId, $titleId, $status);

        // Si se agrega directamente como 'watched', actualizar preferencias de género.
        if ($status === 'watched') {
            $this->applyWatchedPreferences($userId, $titleId);
        }

        return $item;
    }

    public function updateStatus(int $userId, int $titleId, string $status): WatchlistItem
    {
        $this->assertValidStatus($status);

        $item = $this->watchlistRepository->findByUserAndTitle($userId, $titleId);

        if ($item === null) {
            throw new WatchlistItemNotFoundException();
        }

        $previousStatus = $item->status;

        $this->watchlistRepository->updateStatus($userId, $titleId, $status);

        // Actualizar preferencias de género al marcar como visto por primera vez.
        if ($status === 'watched' && $previousStatus !== 'watched') {
            $this->applyWatchedPreferences($userId, $titleId);
        }

        return $this->watchlistRepository->findByUserAndTitle($userId, $titleId);
    }

    /**
     * Ensure a title is in the user's watchlist with status 'watched'.
     *
     * Idempotent — safe to call regardless of whether the item already
     * exists or has a different status.  Used as a side effect when a
     * user writes a review (reviewing implies having watched).
     *
     * No actualiza preferencias de género intencionalmente: ReviewService
     * se encarga de aplicar el delta de la puntuación de la reseña.
     */
    public function ensureWatched(int $userId, int $titleId): void
    {
        $existing = $this->watchlistRepository->findByUserAndTitle($userId, $titleId);

        if ($existing !== null) {
            if ($existing->status !== 'watched') {
                $this->watchlistRepository->updateStatus($userId, $titleId, 'watched');
            }
            return;
        }

        $this->watchlistRepository->insert($userId, $titleId, 'watched');
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

    // ── Privado ───────────────────────────────────────────────────────────────

    private function applyWatchedPreferences(int $userId, int $titleId): void
    {
        $this->preferenceService?->applyWatched($userId, $titleId);
    }
}

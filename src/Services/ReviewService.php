<?php

namespace App\Services;

use App\Core\Exceptions\InvalidValueFormatException;
use App\Core\Exceptions\ReviewAlreadyExistException;
use App\Models\Review;
use App\Repository\ReviewRepository;
use Psr\Log\LoggerInterface;

class ReviewService
{
    private ReviewRepository $reviewRepository;
    private WatchlistService $watchlistService;
    private ?GenrePreferenceService $preferenceService;
    private LoggerInterface $logger;

    public function __construct(
        ReviewRepository        $reviewRepository,
        WatchlistService        $watchlistService,
        LoggerInterface         $logger,
        ?GenrePreferenceService $preferenceService = null,
    ) {
        $this->reviewRepository  = $reviewRepository;
        $this->watchlistService  = $watchlistService;
        $this->logger            = $logger;
        $this->preferenceService = $preferenceService;
    }

    /**
     * @return Review[]
     */
    public function getVisibleByTitleId(int $titleId): array
    {
        return $this->reviewRepository->findVisibleByTitleId($titleId);
    }

    public function getById(int $id): ?Review
    {
        return $this->reviewRepository->findById($id);
    }

    public function getByUserAndTitle(int $userId, int $titleId): ?Review
    {
        return $this->reviewRepository->findByUserAndTitle($userId, $titleId);
    }

    public function createReview(
        int $userId,
        int $titleId,
        float $score,
        ?string $body = null
    ): int {
        if ($titleId <= 0) {
            throw new InvalidValueFormatException('El ID del título no es válido.');
        }

        if ($score < 1 || $score > 5) {
            throw new InvalidValueFormatException(
                'La puntuación debe estar entre 1 y 5.'
            );
        }

        $existing = $this->reviewRepository
            ->findByUserAndTitle($userId, $titleId);

        if ($existing !== null) {
            throw new ReviewAlreadyExistException(
                'El usuario ya escribió una reseña para esta película.'
            );
        }

        $body = trim($body) ?: null;

        $review = new Review(
            null,
            $userId,
            $titleId,
            $score,
            $body
        );

        $reviewId = $this->reviewRepository->save($review);

        // Auto-add to watchlist with 'watched' status —
        // reviewing a film implies having watched it.
        try {
            $this->watchlistService->ensureWatched($userId, $titleId);
        } catch (\Throwable $e) {
            $this->logger->warning('Failed to auto-watchlist after review', [
                'user_id'  => $userId,
                'title_id' => $titleId,
                'error'    => $e->getMessage(),
            ]);
        }

        // Actualizar preferencias de género:
        // +0.10 por marcar como vista (equivalente a haberla marcado manualmente)
        // + delta según puntuación de la reseña.
        try {
            $this->preferenceService?->applyWatched($userId, $titleId);
            $this->preferenceService?->applyReview($userId, $titleId, $score);
        } catch (\Throwable $e) {
            $this->logger->warning('Failed to update genre preferences after review', [
                'user_id'  => $userId,
                'title_id' => $titleId,
                'score'    => $score,
                'error'    => $e->getMessage(),
            ]);
        }

        return $reviewId;
    }

    public function updateReview(
        int $reviewId,
        float $score,
        ?string $body = null
    ): bool {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            return false;
        }

        $review->setScore($score);
        $review->setBody($body);

        return $this->reviewRepository->update($review);
    }

    public function deleteReview(int $reviewId): bool
    {
        return $this->reviewRepository->delete($reviewId);
    }

    public function getByTitleIdWithAuthor(int $titleId): array
    {
        return $this->reviewRepository->findByTitleIdWithAuthorUsername($titleId);
    }
}

<?php

namespace App\Services;

use App\Core\Exceptions\InvalidValueFormatException;
use App\Core\Exceptions\ReviewAlreadyExistException;
use App\Core\Exceptions\ReviewAlreadyReportedException;
use App\Models\Review;
use App\Repository\ReviewReportRepository;
use App\Repository\ReviewRepository;
use PDOException;
use Psr\Log\LoggerInterface;

class ReviewService
{
    private ReviewRepository $reviewRepository;
    private WatchlistService $watchlistService;
    private ?GenrePreferenceService $preferenceService;
    private LoggerInterface $logger;
    private ReviewReportRepository $reviewReportRepository;

    public function __construct(
        ReviewRepository        $reviewRepository,
        WatchlistService        $watchlistService,
        LoggerInterface         $logger,
        ?GenrePreferenceService $preferenceService = null,
        ?ReviewReportRepository $reviewReportRepository = null,
    ) {
        $this->reviewRepository       = $reviewRepository;
        $this->watchlistService       = $watchlistService;
        $this->logger                 = $logger;
        $this->preferenceService      = $preferenceService;
        $this->reviewReportRepository = $reviewReportRepository;
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

    public function getFlagged(): array
    {
        return $this->reviewRepository->findFlagged();
    }

    public function hideReview(int $reviewId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            return false;
        }

        $review->hide();
        return $this->reviewRepository->update($review);
    }

    public function showReview(int $reviewId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            return false;
        }

        $review->show();
        return $this->reviewRepository->update($review);
    }

    public function unflagReview(int $reviewId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            return false;
        }

        $review->unflag();
        return $this->reviewRepository->update($review);
    }

    /**
     * Registra un reporte de un usuario sobre una reseña.
     * Marca la reseña como flagged en el primer reporte.
     * Si alcanza el umbral configurado, se oculta automáticamente.
     *
     * @throws ReviewAlreadyReportedException si el usuario ya reportó esta reseña
     */
    public function reportReview(int $reviewId, int $userId): bool
    {
        try {
            $this->reviewReportRepository->create($reviewId, $userId);
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), '23505')) {
                throw new ReviewAlreadyReportedException();
            }
            throw $e;
        }

        $this->flagReview($reviewId);

        $count = $this->reviewReportRepository->countByReviewId($reviewId);
        $threshold = (int) ($_ENV['REVIEW_REPORT_THRESHOLD'] ?? 3);

        if ($count >= $threshold) {
            $this->hideReview($reviewId);
        }

        return true;
    }

    private function flagReview(int $reviewId): void
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            return;
        }

        $review->flag();
        $this->reviewRepository->update($review);
    }
}

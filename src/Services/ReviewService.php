<?php

namespace App\Services;

use App\Models\Review;
use App\Repository\ReviewRepository;

class ReviewService
{
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
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
        $existing = $this->reviewRepository
            ->findByUserAndTitle($userId, $titleId);
    
        if ($existing !== null) {
            return $existing->getId();
        }
    
        $review = new Review(
            null,
            $userId,
            $titleId,
            $score,
            $body
        );
    
        return $this->reviewRepository->save($review);
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
}
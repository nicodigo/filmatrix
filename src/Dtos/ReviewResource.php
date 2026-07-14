<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Core\Http\Links;
use App\Models\Review;

class ReviewResource
{
    public static function fromReview(Review $review): array
    {
        $id = $review->getId();

        return [
            'id'         => $id,
            'title_id'   => $review->getTitleId(),
            'score'      => $review->getScore(),
            'body'       => $review->getBody(),
            'is_visible' => $review->isVisible(),
            'created_at' => $review->getCreatedAt(),
            'updated_at' => $review->getUpdatedAt(),
            'links'      => Links::build([
                'self'   => ['href' => "/api/v1/reviews?id={$id}", 'method' => 'GET'],
                'update' => ['href' => "/api/v1/reviews?id={$id}", 'method' => 'PATCH'],
                'delete' => ['href' => "/api/v1/reviews?id={$id}", 'method' => 'DELETE'],
                'title'  => ['href' => "/titles/detail?id={$review->getTitleId()}", 'method' => 'GET'],
            ]),
        ];
    }

    public static function collection(array $reviews): array
    {
        return [
            'data'  => array_map([self::class, 'fromReview'], $reviews),
            'links' => Links::build([
                'self'   => ['href' => '/api/v1/reviews', 'method' => 'GET'],
                'create' => ['href' => '/api/v1/reviews', 'method' => 'POST'],
            ]),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Exceptions\InvalidValueFormatException;
use App\Core\Exceptions\ReviewAlreadyExistException;
use App\Core\Http\ApiResponse;
use App\Core\Request;
use App\Dtos\ReviewResource;
use App\Repository\ReviewRepository;
use App\Services\ReviewService;

class ReviewApiController
{
    private ReviewService $reviewService;
    private ReviewRepository $reviewRepository;
    private Request $request;

    public function __construct(ReviewService $reviewService, ReviewRepository $reviewRepository, Request $request)
    {
        $this->reviewService    = $reviewService;
        $this->reviewRepository = $reviewRepository;
        $this->request          = $request;
    }

    // GET /api/v1/reviews
    public function index(int $userId): void
    {
        $reviews = $this->reviewRepository->findByUserId($userId);
        ApiResponse::json(ReviewResource::collection($reviews));
    }

    // GET /api/v1/reviews?id=
    public function show(int $userId): void
    {
        $review = $this->findOwnedOrFail($userId);
        ApiResponse::json(ReviewResource::fromReview($review));
    }

    // POST /api/v1/reviews  body: {title_id, score, body?}
    public function store(int $userId): void
    {
        $body = $this->request->jsonBody();

        try {
            $id = $this->reviewService->createReview(
                $userId,
                (int) ($body['title_id'] ?? 0),
                (float) ($body['score'] ?? 0),
                $body['body'] ?? null
            );
        } catch (InvalidValueFormatException $e) {
            ApiResponse::error(422, $e->getMessage());
        } catch (ReviewAlreadyExistException $e) {
            ApiResponse::error(409, $e->getMessage());
        }

        $review = $this->reviewRepository->findById($id);
        ApiResponse::json(ReviewResource::fromReview($review), 201);
    }

    // PATCH /api/v1/reviews  body: {id, score, body?}
    public function update(int $userId): void
    {
        $this->findOwnedOrFail($userId); // valida ownership antes de escribir

        $body = $this->request->jsonBody();

        try {
            $this->reviewService->updateReview(
                (int) $body['id'],
                (float) ($body['score'] ?? 0),
                $body['body'] ?? null
            );
        } catch (InvalidValueFormatException $e) {
            ApiResponse::error(422, $e->getMessage());
        }

        $review = $this->reviewRepository->findById((int) $body['id']);
        ApiResponse::json(ReviewResource::fromReview($review));
    }

    // DELETE /api/v1/reviews  body: {id}
    public function destroy(int $userId): void
    {
        $this->findOwnedOrFail($userId);

        $body = $this->request->jsonBody();
        $this->reviewService->deleteReview((int) $body['id']);

        ApiResponse::json(['deleted' => true]);
    }

    /**
     * Busca la reseña por id (query string en GET, body en PATCH/DELETE)
     * y devuelve 404 tanto si no existe como si pertenece a otro usuario —
     * no se distingue el mensaje, para no confirmarle a un atacante que el
     * id existe pero es ajeno.
     */
    private function findOwnedOrFail(int $userId)
    {
        $id = (int) ($this->request->get('id') ?? $this->request->jsonBody()['id'] ?? 0);
        $review = $this->reviewRepository->findById($id);

        if (!$review || $review->getUserId() !== $userId) {
            ApiResponse::error(404, 'Reseña no encontrada');
        }

        return $review;
    }
}

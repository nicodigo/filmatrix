<?php

namespace App\Controllers;

use App\Core\Exceptions\ReviewAlreadyExistException;
use App\Services\ReviewService;
use Twig\Environment;

class ReviewController
{
    private Environment $twig;
    private ReviewService $reviewService;

    public function __construct(Environment $twig, ReviewService $reviewService)
    {
        $this->twig = $twig;
        $this->reviewService = $reviewService;
    }

    public function postReview()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $titleId = $_POST['title_id'] ?? null;
        $score = $_POST['score'] ?? null;
        $body = $_POST['review_body'];
        $tmdbId = $_POST['tmdb_id'] ?? null;


        try {
            $this->reviewService->createReview($userId, $titleId, $score, $body);
        } catch (ReviewAlreadyExistException) {
            $_SESSION['flash_error'] = 'Ya escribiste una reseña para esta película';
        } finally {
            header("Location: /movie?tmdb_id={$tmdbId}");
            exit;
        }
    }
}

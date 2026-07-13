<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\ReviewService;
use Twig\Environment;

class AdminReviewController
{
    private Environment $twig;
    private ReviewService $reviewService;
    private Request $request;

    public function __construct(Environment $twig, ReviewService $reviewService, Request $request)
    {
        $this->twig = $twig;
        $this->reviewService = $reviewService;
        $this->request = $request;
    }

    public function index(): void
    {
        echo $this->twig->render(
            'pages/admin/reviews.html.twig',
            [
                'flaggedReviews' => $this->reviewService->getFlagged(),
            ]
        );
    }

    public function hide(): void
    {
        $this->reviewService->hideReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reseña ocultada.');
        header('Location: /admin/reviews');
        exit;
    }

    public function show(): void
    {
        $this->reviewService->showReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reseña restaurada.');
        header('Location: /admin/reviews');
        exit;
    }

    public function unflag(): void
    {
        $this->reviewService->unflagReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reporte descartado.');
        header('Location: /admin/reviews');
        exit;
    }

    public function delete(): void
    {
        $this->reviewService->deleteReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reseña eliminada.');
        header('Location: /admin/reviews');
        exit;
    }
}

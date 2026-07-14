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
        $filters = [
            'filter' => $this->request->get('filter', 'all'),
            'sort'   => $this->request->get('sort', 'date'),
            'dir'    => $this->request->get('dir', 'desc'),
        ];

        echo $this->twig->render(
            'pages/admin/reviews.html.twig',
            [
                'flaggedReviews' => $this->reviewService->getFlagged($filters),
                'flashSuccess'   => $this->request->getFlash('success'),
                'flashError'     => $this->request->getFlash('error'),
                'currentFilter'  => $filters['filter'],
                'currentSort'    => $filters['sort'],
                'currentDir'     => $filters['dir'],
            ]
        );
    }

    public function hide(): void
    {
        $this->reviewService->hideReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reseña ocultada.');
        $this->redirectWithFilters();
    }

    public function show(): void
    {
        $this->reviewService->showReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reseña restaurada.');
        $this->redirectWithFilters();
    }

    public function unflag(): void
    {
        $this->reviewService->unflagReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reporte descartado.');
        $this->redirectWithFilters();
    }

    public function delete(): void
    {
        $this->reviewService->deleteReview((int) $this->request->post('review_id'));
        $this->request->setFlash('success', 'Reseña eliminada.');
        $this->redirectWithFilters();
    }

    private function redirectWithFilters(): void
    {
        $query = http_build_query([
            'filter' => $this->request->post('current_filter', 'all'),
            'sort'   => $this->request->post('current_sort', 'date'),
            'dir'    => $this->request->post('current_dir', 'desc'),
        ]);
        header("Location: /admin/reviews?{$query}");
        exit;
    }
}

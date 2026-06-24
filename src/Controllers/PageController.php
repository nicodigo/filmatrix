<?php

namespace App\Controllers;

use App\Core\Request;
use App\Dtos\TitleCardDto;
use App\Repository\TitleListRepository;
use App\Repository\ReviewRepository;
use Twig\Environment;

class PageController
{
    private TitleListRepository $titleListRepository;
    private ReviewRepository $reviewRepository;
    private Environment $twig;
    private Request $request;

    public function __construct(
        Environment $twig,
        TitleListRepository $titleListRepository,
        ReviewRepository $reviewRepository, 
        Request $request
    ) {
        $this->twig = $twig;
        $this->titleListRepository = $titleListRepository;
        $this->reviewRepository = $reviewRepository; 
        $this->request = $request;
    }
    
    public function home(): void
    {
        $popular = array_map(
            fn(array $row) => new TitleCardDto(
                tmdbId:    (int) $row['tmdb_id'],
                title:     $row['title'],
                posterUrl: $row['poster_url'] ?? null,
                avgScore:  isset($row['avg_score']) ? (float) $row['avg_score'] : null,
            ),
            $this->titleListRepository->findBySection('popular', 12)
        );
    
        $dailyReview = $this->reviewRepository->findLatestWithAuthorAndTitle();
    
        echo $this->twig->render('pages/home.html.twig', [
            'popular'     => $popular,
            'dailyReview' => $dailyReview,
        ]);
    }
    
    public function about(): void
    {
        echo $this->twig->render('pages/about.html.twig');
    }

    public function contact(): void
    {
        echo $this->twig->render('pages/contact.html.twig');
    }
}
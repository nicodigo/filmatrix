<?php

namespace App\Controllers;

use App\Core\Request;
use App\Dtos\TitleCardDto;
use App\Repository\TitleListRepository;
use Twig\Environment;

class PageController
{
    private TitleListRepository $titleListRepository;
    private Environment $twig;
    private Request $request;

    public function __construct(Environment $twig, TitleListRepository $titleListRepository, Request $request)
    {
        $this->twig = $twig;
        $this->titleListRepository = $titleListRepository;
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

        $dailyReview = [
            'title'       => 'Dune: Part Two',
            'year'        => '2024',
            'author'      => 'María López',
            'avatar'      => '/assets/img/user_avatar.png',
            'body' => 'Una obra maestra visual que expande el universo de Frank Herbert con una narrativa épica y actuaciones memorables.',
            'likes'       => 128,
            'url_banner'  => '/assets/img/hero-bg.webp',
        ];

        echo $this->twig->render('pages/home.html.twig', [
            'popular' => $popular,
            'dailyReview' => $dailyReview,
        ]
        );
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
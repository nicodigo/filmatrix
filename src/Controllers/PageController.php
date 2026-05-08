<?php

namespace App\Controllers;

use App\Repository\CatalogListRepository;
use Twig\Environment;

class PageController
{
    private CatalogListRepository $catalogListRepository;
    private Environment $twig;

    public function __construct(Environment $twig, CatalogListRepository $catalogListRepository)
    {
        $this->twig = $twig;
        $this->catalogListRepository = $catalogListRepository;
    }

    public function home(): void
    {
        $popular = $this->catalogListRepository->findBySection('popular', 4);
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
}

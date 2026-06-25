<?php

namespace App\Controllers;

use App\Services\TitleService;
use Twig\Environment;

class SitemapController
{
    private Environment $twig;
    private TitleService $titleService;

    public function __construct(Environment $twig, TitleService $titleService)
    {
        $this->twig = $twig;
        $this->titleService = $titleService;
    }

    public function index(): void
    {
        $titles = $this->titleService->getAllForSitemap();

        $staticPages = [
            ['path' => '/',                    'changefreq' => 'daily',   'priority' => '1.0'],
            ['path' => '/titles',               'changefreq' => 'daily',   'priority' => '0.9'],
            ['path' => '/upcoming',             'changefreq' => 'daily',   'priority' => '0.7'],
            ['path' => '/acerca-de-nosotros',   'changefreq' => 'monthly', 'priority' => '0.3'],
            ['path' => '/contacto',             'changefreq' => 'monthly', 'priority' => '0.3'],
        ];

        header('Content-Type: application/xml; charset=utf-8');

        echo $this->twig->render('sitemap.xml.twig', [
            'staticPages' => $staticPages,
            'titles'      => $titles,
        ]);
    }
}
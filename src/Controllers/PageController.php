<?php
/**
 * PageController 
 * Maneja las páginas estáticas o de contenido general de la aplicación
 * que no pertenecen a un recurso específico (películas, usuarios, etc.).
 *
 * MÉTODOS:
 *   home()
 *     Renderiza la página principal. Obtiene las 4 películas más populares
 *     de la sección 'popular' del catálogo para mostrarlas como destacadas.
 *     Vista: views/pages/home.php
 *     Ruta: GET /
 *
 * DEPENDENCIAS:
 *   CatalogListRepository — para obtener películas destacadas del catálogo.
 *
 * NOTA: Este controller usa el repository directamente ya que es una
 * consulta de solo lectura simple que no requiere lógica de negocio.
 * 
 * (PROXIMA ACTUALIZACION: REEMPLAZAR REPOSITORY POR CatalogSyncService y que no depende del repositorio)
 */

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

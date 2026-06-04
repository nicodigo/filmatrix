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
 *   FilmListRepository — para obtener películas destacadas del catálogo.
 *
 * NOTA: Este controller usa el repository directamente ya que es una
 * consulta de solo lectura simple que no requiere lógica de negocio.
 * 
 * (PROXIMA ACTUALIZACION: REEMPLAZAR REPOSITORY POR FilmSyncService y que no depende del repositorio)
 */

namespace App\Controllers;

use App\Core\Request;
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
        $popular = $this->titleListRepository->findBySection('popular', 4);
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

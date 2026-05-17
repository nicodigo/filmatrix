<?php
/**
 * ReviewController
 * Maneja el envío de reseñas de películas.
 *
 * MÉTODOS:
 *   postReview()
 *     Procesa el formulario de reseña. Verifica que el usuario esté
 *     autenticado, crea la reseña y redirige al detalle de la película.
 *     Si el usuario ya tiene una reseña para ese título, guarda un mensaje
 *     de error en sesión (flash) y redirige igualmente.
 *     Excepción manejada: ReviewAlreadyExistException
 *     Ruta: POST /review
 *
 * DEPENDENCIAS:
 *   ReviewService — lógica de creación de reseñas.
 */
namespace App\Controllers;

use App\Core\Exceptions\ReviewAlreadyExistException;
use App\Core\Request;
use App\Services\ReviewService;
use Twig\Environment;

class ReviewController
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

    public function postReview()
    {
        $userId = $this->request->session('user_id');

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $titleId = $this->request->post('title_id');
        $score = $this->request->post('score');
        $body = $this->request->post('review_body', '');
        $tmdbId = $this->request->post('tmdb_id');


        try {
            $this->reviewService->createReview($userId, $titleId, $score, $body);
        } catch (ReviewAlreadyExistException) {
            $this->request->setSession('flash_error', 'Ya escribiste una reseña para esta película');
        } finally {
            header("Location: /movie?tmdb_id={$tmdbId}");
            exit;
        }
    }
}

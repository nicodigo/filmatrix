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

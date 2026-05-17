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

use App\Core\Exceptions\InvalidValueFormatException;
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

        // Controller-level input validation
        $error = null;

        if (!$titleId || !is_numeric($titleId) || (int) $titleId <= 0) {
            $error = 'El título seleccionado no es válido.';
        } elseif (!$score || !is_numeric($score) || (float) $score < 1 || (float) $score > 5) {
            $error = 'La puntuación debe ser un número entre 1 y 5.';
        }

        if ($error) {
            $this->request->setSession('flash_error', $error);
            header("Location: /movie?tmdb_id={$tmdbId}");
            exit;
        }

        try {
            $this->reviewService->createReview(
                (int) $userId,
                (int) $titleId,
                (float) $score,
                $body
            );
        } catch (ReviewAlreadyExistException) {
            $this->request->setSession(
                'flash_error',
                'Ya escribiste una reseña para esta película.'
            );
        } catch (InvalidValueFormatException $e) {
            $this->request->setSession('flash_error', $e->getMessage());
        } finally {
            header("Location: /movie?tmdb_id={$tmdbId}");
            exit;
        }
    }
}

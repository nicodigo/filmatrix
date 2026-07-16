<?php

/**
 * ReviewController
 * Maneja la creación, edición y eliminación de reseñas de títulos.
 *
 * MÉTODOS:
 *   postReview()
 *     Procesa el formulario de creación de reseña.
 *     Verifica que el usuario esté autenticado, valida los datos
 *     recibidos y crea la reseña.
 *
 *     Si el usuario ya posee una reseña para el mismo título,
 *     guarda un mensaje flash de error y redirige al detalle.
 *
 *     VALIDACIONES:
 *       - title_id válido y mayor a 0.
 *       - score numérico entre 1 y 5.
 *
 *     EXCEPCIONES MANEJADAS:
 *       - ReviewAlreadyExistException
 *       - InvalidValueFormatException
 *
 *     FLASH MESSAGES:
 *       - success → reseña creada correctamente.
 *       - error   → validación o duplicado.
 *
 *     Ruta: POST /review
 *
 *   update()
 *     Actualiza una reseña existente.
 *
 *     Verifica:
 *       - que la reseña exista.
 *       - que pertenezca al usuario autenticado.
 *       - que el score esté entre 1 y 5.
 *
 *     Si la validación falla, guarda un mensaje flash de error
 *     y redirige al detalle del título.
 *
 *     FLASH MESSAGES:
 *       - success → reseña actualizada correctamente.
 *       - error   → permisos inválidos o score incorrecto.
 *
 *     Ruta: POST /review/update
 *
 *   delete()
 *     Elimina una reseña existente.
 *
 *     Verifica:
 *       - que la reseña exista.
 *       - que pertenezca al usuario autenticado.
 *
 *     Si el usuario no tiene permisos sobre la reseña,
 *     guarda un mensaje flash de error y redirige.
 *
 *     FLASH MESSAGES:
 *       - success → reseña eliminada.
 *       - error   → permisos inválidos.
 *
 *     Ruta: POST /review/delete
 *
 * DEPENDENCIAS:
 *   ReviewService — lógica de creación, edición y eliminación de reseñas.
 *   Request       — acceso a request HTTP, sesión y flash messages.
 *   Twig          — motor de templates Twig.
 */

namespace App\Controllers;

use App\Core\Exceptions\InvalidValueFormatException;
use App\Core\Exceptions\ReviewAlreadyExistException;
use App\Core\Exceptions\ReviewAlreadyReportedException;
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

        $titleId = $this->request->post('title_id');
        $score = $this->request->post('score');
        $body = $this->request->post('review_body', '');
        $tmdbId = $this->request->post('tmdb_id');

        // Controller-level input validation
        $error = null;

        if (!$titleId || !is_numeric($titleId) || (int) $titleId <= 0) {
            $error = 'El título seleccionado no es válido.';
        } elseif (!$score || !is_numeric($score) || (float) $score < 1 || (float) $score > 5 || fmod((float) $score * 2, 1) != 0) {
            $error = 'La puntuación debe ser un valor entre 1 y 5 en pasos de 0.5.';
        }

        if ($error) {
            $this->request->setFlash('error', $error);
            header("Location: /titles/detail?tmdb_id={$tmdbId}");
            exit;
        }

        try {
            $this->reviewService->createReview(
                (int) $userId,
                (int) $titleId,
                (float) $score,
                $body
            );
            $this->request->setFlash('success', '¡Reseña publicada con éxito!');
        } catch (ReviewAlreadyExistException) {
            $this->request->setFlash(
                'error',
                'Ya escribiste una reseña para esta película.'
            );
        } catch (InvalidValueFormatException $e) {
            $this->request->setFlash('error', $e->getMessage());
        } finally {
            header("Location: /titles/detail?tmdb_id={$tmdbId}");
            exit;
        }
    }

    public function update(): void
    {
        $userId   = (int) $this->request->session('user_id');
        $reviewId = (int) $this->request->post('review_id');
        $score    = (float) $this->request->post('score');
        $body     = $this->request->post('review_body', '');
        $tmdbId   = $this->request->post('tmdb_id');

        $review = $this->reviewService->getById($reviewId);

        if (!$review || $review->getUserId() !== $userId) {
            $this->request->setFlash('error', 'No tenés permiso para editar esta reseña.');
            $redirect = $this->request->post('redirect', '/titles/detail?tmdb_id=' . $tmdbId);
            header("Location: {$redirect}");
            exit;
        }

        if ($score < 1 || $score > 5 || fmod($score * 2, 1) != 0) {
            $this->request->setFlash('error', 'La puntuación debe ser un valor entre 1 y 5 en pasos de 0.5.');
            $redirect = $this->request->post('redirect', '/titles/detail?tmdb_id=' . $tmdbId);
            header("Location: {$redirect}");
            exit;
        }

        $this->reviewService->updateReview($reviewId, $score, $body);
        $this->request->setFlash('success', 'Reseña actualizada correctamente.');
        $redirect = $this->request->post('redirect', '/titles/detail?tmdb_id=' . $tmdbId);
        header("Location: {$redirect}");
        exit;
    }

    public function delete(): void
    {
        $userId   = (int) $this->request->session('user_id');
        $reviewId = (int) $this->request->post('review_id');
        $tmdbId   = $this->request->post('tmdb_id');

        $review = $this->reviewService->getById($reviewId);

        if (!$review || $review->getUserId() !== $userId) {
            $this->request->setFlash('error', 'No tenés permiso para eliminar esta reseña.');
            $redirect = $this->request->post('redirect', '/titles/detail?tmdb_id=' . $tmdbId);
            header("Location: {$redirect}");
            exit;
        }

        $this->reviewService->deleteReview($reviewId);
        $this->request->setFlash('success', 'Reseña eliminada.');
        $redirect = $this->request->post('redirect', '/titles/detail?tmdb_id=' . $tmdbId);
        header("Location: {$redirect}");
        exit;
    }

    /**
     * Procesa el reporte de una reseña por parte de un usuario.
     *
     * Ruta: POST /review/report
     */
    public function report(): void
    {
        $userId   = (int) $this->request->session('user_id');
        $reviewId = (int) $this->request->post('review_id');
        $tmdbId   = $this->request->post('tmdb_id');

        try {
            $this->reviewService->reportReview($reviewId, $userId);

            $review = $this->reviewService->getById($reviewId);
            if ($review && !$review->isVisible()) {
                $this->request->setFlash('success', 'Reporte enviado. La reseña fue ocultada por exceso de reportes.');
            } else {
                $this->request->setFlash('success', 'Reporte enviado. Gracias.');
            }
        } catch (ReviewAlreadyReportedException $e) {
            $this->request->setFlash('error', $e->getMessage());
        }

        header("Location: /titles/detail?tmdb_id={$tmdbId}");
        exit;
    }
}
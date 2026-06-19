<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Services\RecommendationService;
use RuntimeException;
use Twig\Environment;

class RecommendationController
{
    public function __construct(
        private RecommendationService $recommendationService,
        private Environment           $twig,
        private Request               $request,
    ) {}

    /**
     * GET /recommendations
     * Renderiza la grilla de títulos sugeridos para el usuario autenticado.
     */
    public function index(): void
    {
        $userId = $this->request->session('user_id');

        if ($userId === null) {
            throw new RuntimeException('User not authenticated');
        }

        $titles = $this->recommendationService->getRecommendations($userId);

        $genreBlocks = $this->recommendationService->getGenreBasedRecommendations($userId);

        $this->twig->display('pages/recommendations.html.twig', [
            'titles' => $titles,
            'genreBlocks' => $genreBlocks,
        ]);
    }

    /**
     * POST /recommendations/discard
     * Body JSON: { "title_id": N }
     *
     * Descarta el título: lo excluye de futuras recomendaciones y
     * ajusta las preferencias de género del usuario.
     * Responde con JSON { success: bool }.
     */
    public function discard(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException('User not authenticated');
        }

        $body       = json_decode(file_get_contents('php://input'), true);
        $titleId    = (int) ($body['title_id'] ?? 0);
        $genreId    = isset($body['genre_id']) && $body['genre_id'] !== null
            ? (int) $body['genre_id']
            : null;
        $excludeIds = array_map('intval', $body['visible_ids'] ?? []);

        header('Content-Type: application/json');

        if ($titleId <= 0) {
            echo json_encode(['success' => false, 'error' => 'title_id inválido']);
            return;
        }

        try {
            $this->recommendationService->discard((int) $userId, $titleId);

            $replacement = $genreId !== null
                ? $this->recommendationService->getReplacementByGenre((int) $userId, $genreId, $excludeIds)
                : $this->recommendationService->getReplacement((int) $userId, $excludeIds);

            echo json_encode([
                'success'     => true,
                'replacement' => $replacement,
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

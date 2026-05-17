<?php

namespace App\Services;

use App\Repository\TitleListRepository;
use App\Infrastructure\Tmdb\TmdbClient;
use Psr\Log\LoggerInterface;

class TitleSyncService
{
    private TitleService $titleService;
    private TitleListRepository $titleListRepository;
    private TmdbClient $tmdbClient;
    private LoggerInterface $logger;

    public function __construct(
        TitleService $titleService,
        TitleListRepository $titleListRepository,
        TmdbClient $tmdbClient,
        LoggerInterface $logger
    ) {
        $this->titleService          = $titleService;
        $this->titleListRepository = $titleListRepository;
        $this->tmdbClient            = $tmdbClient;
        $this->logger                = $logger;
    }

    private function syncSection(string $section, array $tmdbResults, int $pageOffset): void
    {
        foreach ($tmdbResults as $i => $movie) {
            try {
                $title = $this->titleService->persistTitle($movie['id']);

                if ($title === null) {
                    continue;
                }

                $this->titleListRepository->insert($section, $title->getId(), $pageOffset + $i);
            } catch (\Throwable $e) {
                $this->logger->warning('Failed to sync movie in section', [
                    'tmdb_id'   => $movie['id'] ?? null,
                    'exception' => $e->getMessage(),
                ]);
            }
        }
    }

    public function syncNowPlaying(int $pages = 1): void
    {
        $this->titleListRepository->clearSection('now_playing');

        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->tmdbClient->getNowPlaying($page);
            $this->syncSection('now_playing', $response['results'], ($page - 1) * 20);
        }

        $this->logger->info('now_playing synced', ['pages' => $pages]);
    }

    public function syncPopular(int $pages = 1): void
    {
        $this->titleListRepository->clearSection('popular');

        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->tmdbClient->getPopular($page);
            $this->syncSection('popular', $response['results'], ($page - 1) * 20);
        }

        $this->logger->info('popular synced', ['pages' => $pages]);
    }

    public function findSuggested(int $excludeTitleId, int $limit): array
    {
        return $this->titleListRepository->findSuggested($excludeTitleId, $limit);
    }

    public function findAllByPopularity(int $limit): array
    {
        return $this->titleListRepository->findAllByPopularity($limit);
    }

    public function findBySection(string $section, int $limit): array
    {
        return $this->titleListRepository->findBySection($section, $limit);
    }
}

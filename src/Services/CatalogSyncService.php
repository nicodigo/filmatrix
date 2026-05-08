<?php
namespace App\Services;

use App\Repository\CatalogListRepository;
use App\Infrastructure\Tmdb\TmdbClient;
use Psr\Log\LoggerInterface;

class CatalogSyncService
{
    private TitleService $titleService;
    private CatalogListRepository $catalogListRepository;
    private TmdbClient $tmdbClient;
    private LoggerInterface $logger;

    public function __construct(
        TitleService $titleService,
        CatalogListRepository $catalogListRepository,
        TmdbClient $tmdbClient,
        LoggerInterface $logger
    ) {
        $this->titleService          = $titleService;
        $this->catalogListRepository = $catalogListRepository;
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

                $this->catalogListRepository->insert($section, $title->getId(), $pageOffset + $i);
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
        $this->catalogListRepository->clearSection('now_playing');

        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->tmdbClient->getNowPlaying($page);
            $this->syncSection('now_playing', $response['results'], ($page - 1) * 20);
        }

        $this->logger->info('now_playing synced', ['pages' => $pages]);
    }

    public function syncPopular(int $pages = 1): void
    {
        $this->catalogListRepository->clearSection('popular');

        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->tmdbClient->getPopular($page);
            $this->syncSection('popular', $response['results'], ($page - 1) * 20);
        }

        $this->logger->info('popular synced', ['pages' => $pages]);
    }

    public function findSuggested(int $excludeTitleId, int $limit): array
    {
        return $this->catalogListRepository->findSuggested($excludeTitleId, $limit);
    }

    public function findAllByPopularity(int $limit): array
    {
        return $this->catalogListRepository->findAllByPopularity($limit);
    }

    public function findBySection(string $section, int $limit): array
    {
        return $this->catalogListRepository->findBySection($section, $limit);
    }
}
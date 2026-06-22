<?php

declare(strict_types=1);

namespace App\Services;

use App\Infrastructure\Tmdb\TmdbClient;
use App\Repository\UpcomingReleaseRepository;
use Psr\Log\LoggerInterface;

class UpcomingReleaseService
{
    public function __construct(
        private UpcomingReleaseRepository $repository,
        private TmdbClient                $tmdbClient,
        private LoggerInterface            $logger,
    ) {}

    /**
     * Sincroniza los próximos estrenos desde TMDB (próximos 90 días).
     * Reemplaza todo el contenido anterior de la tabla.
     */
    public function sync(int $pages = 3): void
    {
        $this->repository->clearAll();

        for ($page = 1; $page <= $pages; $page++) {
            try {
                $response = $this->tmdbClient->getUpcoming($page);
            } catch (\Throwable $e) {
                $this->logger->error('Failed to fetch upcoming releases', [
                    'page'  => $page,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            foreach ($response['results'] ?? [] as $movie) {
                if (empty($movie['release_date'])) {
                    continue;
                }

                try {
                    $this->repository->upsert(
                        (int) $movie['id'],
                        $movie['title'] ?? '',
                        !empty($movie['poster_path'])
                            ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
                            : null,
                        $movie['overview'] ?? null,
                        $movie['release_date'],
                    );
                } catch (\Throwable $e) {
                    $this->logger->warning('Failed to sync upcoming release', [
                        'tmdb_id' => $movie['id'] ?? null,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->logger->info('Upcoming releases synced', ['pages' => $pages]);
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function getByDate(string $date): array
    {
        return $this->repository->findByDate($date);
    }

    public function getByMonth(int $year, int $month): array
    {
        return $this->repository->findGroupedByMonth($year, $month);
    }

    /**
     * Devuelve un mapa fecha => cantidad de estrenos, para marcar
     * los días con punto en la grilla del calendario.
     *
     * @return array<string, int> 'Y-m-d' => count
     */
    public function getCountsByMonth(int $year, int $month): array
    {
        $releases = $this->getByMonth($year, $month);

        $counts = [];
        foreach ($releases as $release) {
            $counts[$release->releaseDate] = ($counts[$release->releaseDate] ?? 0) + 1;
        }

        return $counts;
    }
}
<?php

namespace App\Services;

use App\Infrastructure\Tmdb\TmdbClient;
use App\Dtos\CatalogQuery;
use App\Dtos\CatalogResult;
use App\Models\Title;
use App\Dtos\TitleCardDto;
use App\Repository\TitleRepository;
use Psr\Log\LoggerInterface;

class TitleService
{
    private TitleRepository $titleRepository;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private TmdbClient $tmdbClient;
    private LoggerInterface $logger;
    private const int PER_PAGE = 40;
    private const int TMDB_MAX_PAGES = 500;

    public function __construct(
        TitleRepository $titleRepository,
        GenreService $genreService,
        PeopleService $peopleService,
        TmdbClient $tmdbClient,
        LoggerInterface $logger,
    ) {
        $this->titleRepository       = $titleRepository;
        $this->genreService          = $genreService;
        $this->peopleService         = $peopleService;
        $this->tmdbClient            = $tmdbClient;
        $this->logger                = $logger;
    }

    public function getTitle(int $tmdbId): Title
    {
        $title = $this->titleRepository->findByTmdbId($tmdbId);

        if ($title === null) {
            $title = $this->syncTitleWithTmdb($tmdbId);
        }

        return $title;
    }

    /* =========================
       SYNC TMDB
    ========================= */

    public function syncTitleWithTmdb(int $tmdbId): Title
    {
        $movie  = $this->tmdbClient->getMovie($tmdbId);
        $videos = $this->tmdbClient->getVideos($tmdbId);

        $trailerUrl = null;

        foreach ($videos['results'] ?? [] as $video) {
            if (($video['site'] ?? '') === 'YouTube' &&
                ($video['type'] ?? '') === 'Trailer'
            ) {
                $trailerUrl = 'https://www.youtube.com/watch?v=' . $video['key'];
                break;
            }
        }

        $releaseYear = !empty($movie['release_date'])
            ? (int) date('Y', strtotime($movie['release_date']))
            : null;

        $title = new Title(
            null,
            $movie['id'],
            'movie',
            $movie['title'],
            $movie['overview'] ?? null,
            !empty($movie['poster_path'])
                ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
                : null,
            $trailerUrl,
            $releaseYear,
            $movie['original_language'] ?? null,
            $movie['runtime'] ?? null,
        );

        $titleId = $this->titleRepository->upsert($title);

        /* genres */
        $this->titleRepository->clearGenres($titleId);

        foreach ($movie['genres'] ?? [] as $genre) {
            $genreId = $this->genreService->sync($genre['id'], $genre['name']);
            $this->titleRepository->attachGenre($titleId, $genreId);
        }

        /* cast */
        $this->titleRepository->clearCast($titleId);

        $credits = $this->tmdbClient->getCredits($tmdbId);

        foreach (array_slice($credits['cast'] ?? [], 0, 10) as $member) {
            $personId = $this->peopleService->sync(
                $member['id'],
                $member['name'],
            );

            $this->titleRepository->attachCastMember(
                $titleId,
                $personId,
                'actor',
                $member['character'] ?? null,
                $member['order'] ?? 0
            );
        }

        /* directors */
        foreach ($credits['crew'] ?? [] as $member) {
            if (($member['job'] ?? '') === 'Director') {
                $personId = $this->peopleService->sync(
                    $member['id'],
                    $member['name'],
                );

                $this->titleRepository->attachCastMember(
                    $titleId,
                    $personId,
                    'director',
                    null,
                    0
                );
            }
        }

        $this->logger->info('Title synced', ['tmdb_id' => $tmdbId]);

        $title = $this->titleRepository->findByTmdbId($tmdbId);

        if ($title === null) {
            throw new \RuntimeException("Título sincronizado, pero no encontrado en la db: {$tmdbId}");
        }

        return $title;
    }

    public function syncGenres(): void
    {
        $genres = $this->tmdbClient->getGenres();

        foreach ($genres['genres'] ?? [] as $genre) {
            $this->genreService->sync($genre['id'], $genre['name']);
        }
    }

    public function getCatalog(CatalogQuery $query): CatalogResult
    {
        return $this->catalogFromLocal($query);
    }

    public function search(string $query, int $page = 1): CatalogResult
    {
        try {
            $response = $this->tmdbClient->searchMovie($query, $page);
        } catch (\Throwable $e) {
            $this->logger->error('Search TMDB failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            return new CatalogResult([], $page, 1, 'tmdb');
        }

        return $this->buildTmdbResult($response, $page);
    }

    private function catalogFromLocal(CatalogQuery $query): CatalogResult
    {
        $offset = ($query->page - 1) * self::PER_PAGE;

        $titles = $this->titleRepository->filter(
            $query->genreId,
            $query->year,
            $query->language,
            $query->minScore,
            self::PER_PAGE,
            $offset,
            $query->sort
        );

        $total      = $this->titleRepository->filterCount(
            $query->genreId,
            $query->year,
            $query->language,
            $query->minScore
        );
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));

        $items = array_map(
            fn(Title $t) => new TitleCardDto(
                $t->getTmdbId(),
                $t->getTitle(),
                $t->getPosterUrl(),
                $t->getAvgScore(),
            ),
            $titles
        );

        return new CatalogResult($items, $query->page, $totalPages, 'local');
    }

    // Lógica de construcción compartida entre discover y search
    private function buildTmdbResult(array $response, int $page): CatalogResult
    {
        $results = array_filter(
            $response['results'] ?? [],
            fn($item) => empty($item['adult']) || $item['adult'] !== true
        );

        $tmdbIds     = array_map(fn($item) => (int) $item['id'], $results);
        $localScores = $this->titleRepository->findAvgScoresForTmdbIds($tmdbIds);

        $items = [];
        foreach ($results as $movie) {
            $tmdbId  = (int) $movie['id'];
            $items[] = new TitleCardDto(
                $tmdbId,
                $movie['title'] ?? '',
                !empty($movie['poster_path'])
                    ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path']
                    : null,
                $localScores[$tmdbId] ?? null,
            );
        }

        $totalPages = min(
            (int) ($response['total_pages'] ?? 1),
            self::TMDB_MAX_PAGES
        );

        return new CatalogResult($items, $page, $totalPages, 'tmdb');
    }
}

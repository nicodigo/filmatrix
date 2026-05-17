<?php

namespace App\Services;

use App\Core\Config;
use App\Infrastructure\Tmdb\TmdbClient;
use App\Models\Title;
use App\Repository\TitleRepository;
use DateTime;

class TitleService
{
    private TitleRepository $titleRepository;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private TmdbClient $tmdbClient;
    private Config $config;

    public function __construct(
        TitleRepository $titleRepository,
        GenreService $genreService,
        PeopleService $peopleService,
        TmdbClient $tmdbClient,
        Config $config,
    ) {
        $this->titleRepository       = $titleRepository;
        $this->genreService          = $genreService;
        $this->peopleService         = $peopleService;
        $this->tmdbClient            = $tmdbClient;
        $this->config                = $config;
    }

    /* =========================
       CACHE / CORE
    ========================= */

    private function isCacheStale(?Title $title): bool
    {
        if ($title === null) {
            return true;
        }

        $ttl = (int) ($this->config->get('TMDB_CACHE_TTL_DAYS') ?? 30);

        $cachedAt = new DateTime($title->getCachedAt());
        $now      = new DateTime();

        return $cachedAt->diff($now)->days > $ttl;
    }

    public function getTitle(int $tmdbId): Title
    {
        $title = $this->titleRepository->findByTmdbIdWithScore($tmdbId);

        if ($this->isCacheStale($title)) {
            return $this->persistTitle($tmdbId);
        }

        return $title;
    }

    /* =========================
       DETAIL HELPERS
    ========================= */

    public function getTitleGenres(int $titleId): array
    {
        return $this->titleRepository->findGenresByTitleId($titleId);
    }

    public function getTitleCast(int $titleId): array
    {
        return $this->titleRepository->findCastByTitleId($titleId);
    }

    /* =========================
       SYNC TMDB
    ========================= */

    public function persistTitle(int $tmdbId): Title
    {
        $movie  = $this->tmdbClient->getMovie($tmdbId);
        $videos = $this->tmdbClient->getVideos($tmdbId);

        $trailerUrl = null;

        foreach ($videos['results'] ?? [] as $video) {
            if (($video['site'] ?? '') === 'YouTube' &&
                ($video['type'] ?? '') === 'Trailer') {
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
            (float) ($movie['vote_average'] ?? 0)
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
                !empty($member['profile_path'])
                    ? 'https://image.tmdb.org/t/p/w185' . $member['profile_path']
                    : null
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
                    !empty($member['profile_path'])
                        ? 'https://image.tmdb.org/t/p/w185' . $member['profile_path']
                        : null
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

        return $this->titleRepository->findByTmdbIdWithScore($tmdbId);
    }

    public function syncGenres(): void
    {
        $genres = $this->tmdbClient->getGenres();

        foreach ($genres['genres'] ?? [] as $genre) {
            $this->genreService->sync($genre['id'], $genre['name']);
        }
    }
}

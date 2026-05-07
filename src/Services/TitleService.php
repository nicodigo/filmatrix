<?php

namespace App\Services;

use App\Core\Config;
use App\Core\TmdbClient;
use App\Repository\GenreRepository;
use App\Repository\PeopleRepository;
use App\Repository\TitleRepository;
use DateTime;
use Psr\Log\LoggerInterface;

class TitleService
{
    private const TMDB_IMAGE_BASE = 'https://image.tmdb.org/t/p/w500';
    private const TMDB_PROFILE_BASE = 'https://image.tmdb.org/t/p/w185';
    private const TMDB_YOUTUBE_BASE = 'https://www.youtube.com/watch?v=';

    private TitleRepository $titleRepository;
    private GenreRepository $genreRepository;
    private PeopleRepository $peopleRepository;
    private TmdbClient $tmdbClient;
    private Config $config;
    private LoggerInterface $logger;

    public function __construct(
        TitleRepository $titleRepository,
        GenreRepository $genreRepository,
        PeopleRepository $peopleRepository,
        TmdbClient $tmdbClient,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->titleRepository = $titleRepository;
        $this->genreRepository = $genreRepository;
        $this->peopleRepository = $peopleRepository;
        $this->tmdbClient = $tmdbClient;
        $this->config = $config;
        $this->logger = $logger;
    }

    private function isCacheStale(?array $row): bool
    {
        if ($row === null) {
            return true;
        }

        $ttl = (int) ($this->config->get('TMDB_CACHE_TTL_DAYS') ?? 30);

        $cachedAt = new DateTime($row['cached_at']);
        $now = new DateTime();

        $diff = $cachedAt->diff($now);

        return $diff->days > $ttl;
    }

    private function extractTrailerUrl(array $videosResponse): ?string
    {
        $results = $videosResponse['results'] ?? [];

        foreach ($results as $video) {
            if (($video['site'] ?? '') === 'YouTube' && ($video['type'] ?? '') === 'Trailer') {
                return self::TMDB_YOUTUBE_BASE . $video['key'];
            }
        }

        return null;
    }

    private function persistTitle(int $tmdbId): array
    {
        $movie = $this->tmdbClient->getMovie($tmdbId);
        $videosResponse = $this->tmdbClient->getVideos($tmdbId);
        $trailerUrl = $this->extractTrailerUrl($videosResponse);

        $releaseYear = null;
        if (!empty($movie['release_date'])) {
            $releaseYear = (int) date('Y', strtotime($movie['release_date']));
        }

        $titleId = $this->titleRepository->upsert([
            'tmdb_id' => $movie['id'],
            'type' => 'movie',
            'title' => $movie['title'],
            'synopsis' => $movie['overview'] ?? null,
            'poster_url' => $movie['poster_path'] ? self::TMDB_IMAGE_BASE . $movie['poster_path'] : null,
            'trailer_url' => $trailerUrl,
            'release_year' => $releaseYear,
            'language' => $movie['original_language'] ?? null,
            'duration_minutes' => $movie['runtime'] ?? null,
            'tmdb_rating' => $movie['vote_average'] ?? null,
        ]);

        $credits = $this->tmdbClient->getCredits($tmdbId);

        // sync genres
        $this->titleRepository->clearGenres($titleId);
        foreach ($movie['genres'] ?? [] as $genre) {
            $genreId = $this->genreRepository->upsert($genre['id'], $genre['name']);
            $this->titleRepository->attachGenre($titleId, $genreId);
        }

        // sync cast (first 10 actors)
        $this->titleRepository->clearCast($titleId);
        $cast = array_slice($credits['cast'] ?? [], 0, 10);
        foreach ($cast as $member) {
            $profileUrl = $member['profile_path'] ? self::TMDB_PROFILE_BASE . $member['profile_path'] : null;
            $personId = $this->peopleRepository->upsert(
                $member['id'],
                $member['name'],
                $profileUrl
            );
            $this->titleRepository->attachCastMember(
                $titleId,
                $personId,
                'actor',
                $member['character'] ?? null,
                $member['order']
            );
        }

        // sync directors
        foreach ($credits['crew'] ?? [] as $member) {
            if (($member['job'] ?? '') === 'Director') {
                $profileUrl = $member['profile_path'] ? self::TMDB_PROFILE_BASE . $member['profile_path'] : null;
                $personId = $this->peopleRepository->upsert(
                    $member['id'],
                    $member['name'],
                    $profileUrl
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

        $this->logger->info('Title cached from TMDB', ['tmdb_id' => $tmdbId]);

        return $this->titleRepository->findByTmdbId($tmdbId);
    }

    public function getTitle(int $tmdbId): array
    {
        $row = $this->titleRepository->findByTmdbId($tmdbId);

        if ($this->isCacheStale($row)) {
            return $this->persistTitle($tmdbId);
        }

        return $row;
    }

    public function syncGenres(): void
    {
        $response = $this->tmdbClient->getGenres();

        foreach ($response['genres'] ?? [] as $genre) {
            $this->genreRepository->upsert($genre['id'], $genre['name']);
        }

        $this->logger->info('Genres synced from TMDB', ['count' => count($response['genres'] ?? [])]);
    }
}

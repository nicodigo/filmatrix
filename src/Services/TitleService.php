<?php

namespace App\Services;

use App\Infrastructure\Tmdb\TmdbClient;
use App\Models\Title;
use App\Repository\TitleRepository;
use Psr\Log\LoggerInterface;

class TitleService
{
    private TitleRepository $titleRepository;
    private GenreService $genreService;
    private PeopleService $peopleService;
    private TmdbClient $tmdbClient;
    private LoggerInterface $logger;
    private const int TITLE_CACHE_TTL = 30; //days

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
        $title = $this->titleRepository->findByTmdbId($tmdbId, self::TITLE_CACHE_TTL);

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

        $title = $this->titleRepository->findByTmdbId($tmdbId, self::TITLE_CACHE_TTL);

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
    
    public function search(string $query): array
    {
        return $this->titleRepository->search($query);
    }

    public function filter(
        ?int $genreId,
        ?int $year,
        ?string $language,
        ?float $minScore
    ): array {
        return $this->titleRepository->filter($genreId, $year, $language, $minScore);
    }
}

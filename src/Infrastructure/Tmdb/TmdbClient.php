<?php

namespace App\Infrastructure\Tmdb;

use App\Core\Config;
use App\Core\Exceptions\TmdbApiException;
use App\Core\Traits\Loggable;

class TmdbClient
{
    use Loggable;

    private string $baseUrl = 'https://api.themoviedb.org/3';
    private string $token;

    public function __construct(private Config $config)
    {
        $this->token = $this->config->get('TMDB_READ_ACCESS_TOKEN');
    }

    private function request(string $endpoint, array $queryParams = []): array
    {
        $params = array_merge($queryParams, ['language' => 'es-AR']);
        $url = $this->baseUrl . $endpoint . '?' . http_build_query($params);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($curlError !== '') {
            $this->logger->error($curlError);
            throw new TmdbApiException($curlError);
        }

        if ($httpCode !== 200) {
            $this->logger->error("TMDB API error {$httpCode} on {$endpoint}");
            throw new TmdbApiException("TMDB API error {$httpCode} on {$endpoint}");
        }

        $data = json_decode($response, true);
        return $data ?? [];
    }

    public function getMovie(int $tmdbId): array
    {
        return $this->request("/movie/{$tmdbId}");
    }

    public function getCredits(int $tmdbId): array
    {
        return $this->request("/movie/{$tmdbId}/credits");
    }

    public function getVideos(int $tmdbId): array
    {
        return $this->request("/movie/{$tmdbId}/videos");
    }

    public function getGenres(): array
    {
        return $this->request('/genre/movie/list');
    }

    public function searchMovies(string $query, int $page = 1): array
    {
        return $this->request('/search/movie', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    public function getNowPlaying(int $page = 1): array
    {
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        return $this->request('/discover/movie', [
            'sort_by' => 'primary_release_date.desc',
            'include_adult' => 'false',
            'with_release_type' => '2|3',
            'primary_release_date.gte' => $startDate,
            'primary_release_date.lte' => $endDate,
            'page' => $page,
        ]);
    }

    public function getPopular(int $page = 1): array
    {
        return $this->request('/discover/movie', [
            'sort_by' => 'popularity.desc',
            'include_adult' => 'false',
            'vote_count.gte' => 500,
            'page' => $page,
        ]);
    }
}

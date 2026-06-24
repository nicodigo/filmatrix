<?php

/**
 * TmdbClient — Cliente HTTP para la API de The Movie Database (TMDB)
 * ─────────────────────────────────────────────────────────────────────
 * Centraliza todas las llamadas a la API de TMDB. El resto de la aplicación
 * (services, scripts de sync) nunca habla directo con TMDB — siempre pasan
 * por esta clase.
 *
 * AUTENTICACIÓN:
 *   Usa Bearer Token (TMDB_READ_ACCESS_TOKEN en el .env).
 *   Todas las respuestas vienen en español argentino (language=es-AR).
 *
 * MANEJO DE ERRORES:
 *   Lanza TmdbApiException si hay error de red (cURL) o si TMDB devuelve
 *   un código HTTP distinto de 200.
 *
 * MÉTODOS DISPONIBLES:
 *
 *   getMovie(tmdbId)
 *     Datos completos de una película: título, sinopsis, póster, duración,
 *     géneros, rating, etc.
 *     Endpoint: GET /movie/{tmdbId}
 *
 *   getCredits(tmdbId)
 *     Reparto (cast) y equipo técnico (crew) de una película.
 *     Usado para extraer actores y directores.
 *     Endpoint: GET /movie/{tmdbId}/credits
 *
 *   getVideos(tmdbId)
 *     Videos asociados a una película (trailers, teasers, etc.).
 *     Usado para obtener el trailer de YouTube.
 *     Endpoint: GET /movie/{tmdbId}/videos
 *
 *   getGenres()
 *     Lista completa de géneros cinematográficos de TMDB.
 *     Usado por syncGenres() para pre-cargar la tabla `genres`.
 *     Endpoint: GET /genre/movie/list
 *
 *   getNowPlaying(page)
 *     Películas estrenadas en los últimos 30 días, ordenadas por fecha de
 *     estreno descendente. Filtra por tipo de estreno theatrical (2|3).
 *     Endpoint: GET /discover/movie (con filtros de fecha)
 *
 *   getPopular(page)
 *     Películas ordenadas por popularidad descendente, con al menos 500
 *     votos para filtrar títulos sin relevancia.
 *     Endpoint: GET /discover/movie (con sort_by=popularity.desc)
 *
 *   getUpcoming(page)
 *     Películas con estreno futuro (a partir de mañana), ordenadas por
 *     fecha de estreno ascendente. Usado para poblar la sección
 *     "Próximos estrenos" con su calendario.
 *     Endpoint: GET /discover/movie (con filtro de fecha futura)
 */
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

    public function getUpcoming(int $page = 1): array
    {
        $startDate = date('Y-m-d', strtotime('+1 day'));
        $endDate   = date('Y-m-d', strtotime('+120 days')); 

        $results = $this->request('/discover/movie', [
            'sort_by'                  => 'popularity.desc',
            'include_adult'            => 'false',
            'with_release_type'        => '2|3',
            'primary_release_date.gte' => $startDate,
            'primary_release_date.lte' => $endDate,
            'page'                     => $page,
        ]);
        
        return $results;
    }

    public function searchMovie(string $query, int $page = 1): array
    {
        return $this->request('/search/movie', [
            'query' => $query,
            'include_adult' => 'false',
            'page' => $page,
        ]);
    }
}
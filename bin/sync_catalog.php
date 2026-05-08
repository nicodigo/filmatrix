<?php

/**
 * sync_catalog.php — Script CLI de sincronización de catálogo
 * ─────────────────────────────────────────────────────────────
 * Sincroniza películas desde la API de TMDB hacia la base de datos local.
 *
 * USO:
 *   php sync_catalog.php [--section=<sección>] [--pages=<n>]
 *
 * OPCIONES:
 *   --section   Sección a sincronizar. Valores: all | now_playing | popular
 *               Default: all
 *   --pages     Cantidad de páginas de TMDB a traer (1 página = 20 películas).
 *               Default: 1
 *
 * EJEMPLOS:
 *   php sync_catalog.php                          # sincroniza todo, 1 página
 *   php sync_catalog.php --section=popular        # solo popular, 1 página
 *   php sync_catalog.php --section=all --pages=3  # todo, 3 páginas (60 películas)
 *
 * SECCIONES:
 *   now_playing  Películas estrenadas en los últimos 30 días, ordenadas por
 *                fecha de estreno descendente.
 *   popular      Películas ordenadas por popularidad descendente, con al
 *                menos 500 votos en TMDB.
 *
 * FLUJO DE INSERCIÓN POR PELÍCULA:
 *   Ambas secciones pasan por el mismo flujo interno (syncSection → persistTitle):
 *
 *   1. `titles`       — upsert de los datos de la película (título, sinopsis,
 *                       póster, trailer, año, duración, rating TMDB, etc.)
 *   2. `genres`       — upsert de cada género de la película
 *   3. `title_genres` — insert en tabla intermedia título ↔ género
 *   4. `people`       — upsert de cada actor (top 10) y director
 *   5. `title_cast`   — insert en tabla intermedia título ↔ persona (con rol,
 *                       nombre del personaje y orden de crédito)
 *   6. `catalog_lists`— insert de la película en la sección correspondiente
 *                       con su posición de popularidad (0 = más popular)
 *
 * NOTA: Antes de sincronizar cualquier sección, el script pre-carga todos los
 * géneros de TMDB en la tabla `genres` via syncGenres(). Esto asegura que los
 * géneros existan antes de que las películas intenten referenciarlos.
 * Aunque no fuera necesario (persistTitle los inserta igual), es una buena
 * práctica para mantener la tabla genres completa y actualizada.
 */
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;

use App\Core\Config;
use App\Core\Database\ConnectionBuilder;
use App\Infrastructure\Tmdb\TmdbClient;
use App\Repository\TitleRepository;
use App\Repository\GenreRepository;
use App\Repository\PeopleRepository;
use App\Repository\CatalogListRepository;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\TitleService;
use App\Services\CatalogSyncService;

// 1. Load environment
Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

// 2. Config
$config = new Config();

// 3. Logger
$logger  = new Logger('sync-catalog');
$handler = new StreamHandler($config->get('LOG_PATH'));
$handler->setLevel($config->get('LOG_LEVEL'));
$logger->pushHandler($handler);

// 4. Connection
$connectionBuilder = new ConnectionBuilder();
$connectionBuilder->setLogger($logger);
$connection = $connectionBuilder->make($config);

// 5. Repositories
$titleRepository       = new TitleRepository($connection);
$genreRepository       = new GenreRepository($connection);
$peopleRepository      = new PeopleRepository($connection);
$catalogListRepository = new CatalogListRepository($connection);

// 6. TMDB client
$tmdbClient = new TmdbClient($config);

// 7. Services
$genreService  = new GenreService($genreRepository);
$peopleService = new PeopleService($peopleRepository);

$titleService = new TitleService(
    $titleRepository,
    $genreService,
    $peopleService,
    $tmdbClient,
    $config,
    $logger,
    $catalogListRepository
);

$catalogSyncService = new CatalogSyncService(
    $titleService,
    $catalogListRepository,
    $tmdbClient,
    $logger
);

// 8. Parse CLI arguments
$section = 'all';
$pages   = 1;

for ($i = 1; $i < count($argv); $i++) {
    $arg = $argv[$i];
    if (str_starts_with($arg, '--section=')) {
        $section = substr($arg, strlen('--section='));
    } elseif (str_starts_with($arg, '--pages=')) {
        $pages = max(1, (int) substr($arg, strlen('--pages=')));
    }
}

$allowedSections = ['all', 'now_playing', 'popular'];

if (!in_array($section, $allowedSections, true)) {
    echo "Error: sección inválida '$section'. Valores permitidos: all, now_playing, popular\n";
    exit(1);
}

// 9. Sync
try {
    echo "Sincronizando géneros...\n";
    $titleService->syncGenres();
    echo "OK\n\n";

    if ($section === 'all' || $section === 'now_playing') {
        echo "Sincronizando now_playing ({$pages} página/s)...\n";
        // Aca ocurre la sincronizacion de pelicula recientes
        $catalogSyncService->syncNowPlaying($pages);
        echo "OK\n\n";
    }

    if ($section === 'all' || $section === 'popular') {
        echo "Sincronizando popular ({$pages} página/s)...\n";
         // Aca ocurre la sincronizacion de pelicula populares
        $catalogSyncService->syncPopular($pages);
        echo "OK\n\n";
    }

    echo "Sincronización completada.\n";
} catch (\Throwable $e) {
    echo "Error: {$e->getMessage()}\n";
    $logger->error('Sync failed', ['exception' => $e->getMessage()]);
    exit(1);
}
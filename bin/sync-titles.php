<?php

/**
 * sync-titles.php — Script CLI de sincronización de catálogo
 * ─────────────────────────────────────────────────────────────
 * Sincroniza títulos desde la API de TMDB hacia la base de datos local.
 *
 * USO:
 *   php bin/sync-titles.php [--section=<sección>] [--pages=<n>]
 *
 * OPCIONES:
 *   --section   Sección a sincronizar. Valores: all | now_playing | popular
 *               Default: all
 *   --pages     Cantidad de páginas de TMDB a traer (1 página = 20 títulos).
 *               Default: 1
 *
 * EJEMPLOS:
 *   php bin/sync-titles.php                          # sincroniza todo, 1 página
 *   php bin/sync-titles.php --section=popular        # solo popular, 1 página
 *   php bin/sync-titles.php --section=all --pages=3  # todo, 3 páginas
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
use App\Repository\TitleListRepository;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\TitleService;
use App\Services\TitleSyncService;

// 1. Load environment
Dotenv::createUnsafeImmutable(__DIR__ . '/../')->safeLoad();

// 2. Config
$config = new Config();

// 3. Logger
$logger  = new Logger('sync-titles');
$handler = new StreamHandler('php://stderr');
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
$titleListRepository = new TitleListRepository($connection);

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
);

$titleSyncService = new TitleSyncService(
    $titleService,
    $titleListRepository,
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
        $titleSyncService->syncNowPlaying($pages);
        echo "OK\n\n";
    }

    if ($section === 'all' || $section === 'popular') {
        echo "Sincronizando popular ({$pages} página/s)...\n";
        $titleSyncService->syncPopular($pages);
        echo "OK\n\n";
    }

    echo "Sincronización completada.\n";
} catch (\Throwable $e) {
    echo "Error: {$e->getMessage()}\n";
    $logger->error('Sync failed', ['exception' => $e->getMessage()]);
    exit(1);
}

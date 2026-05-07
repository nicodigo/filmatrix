<?php

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
use App\Services\CatalogSyncService;
use App\Services\TitleService;

// 1. Load environment
Dotenv::createUnsafeImmutable(__DIR__ . '/../')->load();

// 2. Config
$config = new Config();

// 3. Logger
$logger = new Logger('sync-catalog');
$handler = new StreamHandler($config->get('LOG_PATH'));
$handler->setLevel($config->get('LOG_LEVEL'));
$logger->pushHandler($handler);

// 4. Connection
$connectionBuilder = new ConnectionBuilder();
$connectionBuilder->setLogger($logger);
$connection = $connectionBuilder->make($config);

// 5. Repositories
$titleRepository = new TitleRepository($connection);
$genreRepository = new GenreRepository($connection);
$peopleRepository = new PeopleRepository($connection);
$catalogListRepository = new CatalogListRepository($connection);

// 6. TMDB client
$tmdbClient = new TmdbClient($config);

// 7. TitleService
$titleService = new TitleService(
    $titleRepository,
    $genreRepository,
    $peopleRepository,
    $tmdbClient,
    $config,
    $logger
);

// 8. CatalogSyncService
$catalogSyncService = new CatalogSyncService(
    $titleService,
    $titleRepository,
    $catalogListRepository,
    $tmdbClient,
    $logger
);

// Parse CLI arguments
$section = 'all';
$pages = 1;

for ($i = 1; $i < count($argv); $i++) {
    $arg = $argv[$i];
    if (str_starts_with($arg, '--section=')) {
        $section = substr($arg, strlen('--section='));
    } elseif (str_starts_with($arg, '--pages=')) {
        $pages = (int) substr($arg, strlen('--pages='));
        if ($pages < 1) {
            $pages = 1;
        }
    }
}

$allowedSections = ['all', 'now_playing', 'popular'];
if (!in_array($section, $allowedSections, true)) {
    echo "Error: Invalid --section value '$section'. Allowed values: all, now_playing, popular\n";
    exit(1);
}

try {
    if ($section === 'all') {
        echo "Syncing genres...\n";
        $titleService->syncGenres();
        echo "Done.\n";

        echo "Syncing now_playing...\n";
        $catalogSyncService->syncNowPlaying($pages);
        echo "Done.\n";

        echo "Syncing popular...\n";
        $catalogSyncService->syncPopular($pages);
        echo "Done.\n";
    } elseif ($section === 'now_playing') {
        echo "Syncing now_playing...\n";
        $titleService->syncGenres();
        echo "Done.\n";

        echo "Syncing now_playing...\n";
        $catalogSyncService->syncNowPlaying($pages);
        echo "Done.\n";
    } elseif ($section === 'popular') {
        echo "Syncing popular...\n";
        $titleService->syncGenres();
        echo "Done.\n";

        echo "Syncing popular...\n";
        $catalogSyncService->syncPopular($pages);
        echo "Done.\n";
    }
} catch (\Throwable $e) {
    echo "Error: {$e->getMessage()}\n";
    exit(1);
}

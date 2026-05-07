<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;

use App\Core\Router;
use App\Core\Config;
use App\Core\Database\ConnectionBuilder;
use App\Core\Request;
use App\Repository\UserRepository;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
use App\Infrastructure\Tmdb\TmdbClient;
use App\Repository\TitleRepository;
use App\Repository\GenreRepository;
use App\Repository\PeopleRepository;
use App\Repository\CatalogListRepository;
use App\Services\TitleService;
use App\Services\CatalogSyncService;
use App\Controllers\CatalogController;
use App\Repository\ReviewRepository;
use App\Controllers\MovieController;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

$config = new Config();

$log_app = new Logger('log-app');
$handler = new StreamHandler($config->get('LOG_PATH'));
$handler->setLevel($config->get('LOG_LEVEL'));
$log_app->pushHandler($handler);

$connectionBuilder = new ConnectionBuilder();
$connectionBuilder->setLogger($log_app);
$connection = $connectionBuilder->make($config);


$userRepository = new UserRepository($connection);
$authService = new AuthService($userRepository, $log_app);
$userService = new \App\Services\UserService($userRepository);

$titleRepository = new TitleRepository($connection);
$genreRepository = new GenreRepository($connection);
$peopleRepository = new PeopleRepository($connection);
$catalogListRepository = new CatalogListRepository($connection);
$reviewRepository = new ReviewRepository($connection);

$tmdbClient = new TmdbClient($config);
$titleService = new TitleService(
    $titleRepository,
    $genreRepository,
    $peopleRepository,
    $tmdbClient,
    $config,
    $log_app
);

$catalogSyncService = new CatalogSyncService(
    $titleService,
    $titleRepository,
    $catalogListRepository,
    $tmdbClient,
    $log_app
);

$authMiddleware = new AuthMiddleware();

$request = new Request();

// Factories de controllers
$makeUserCtrl = fn() => new \App\Controllers\UserController($authService, $userService);
$makePageCtrl = fn() => new \App\Controllers\PageController($catalogListRepository);
$makeCatalogCtrl = fn() => new CatalogController($titleService, $catalogListRepository);
$makeMovieCtrl = fn() => new MovieController(
    $titleService,
    $reviewRepository,
    $catalogSyncService
);

// Helper para rutas protegidas
$protegida = fn(callable $action) => function() use ($authMiddleware, $action) {
    $authMiddleware->handle();
    return $action();
};

$router = new Router();
$router->setLogger($log_app);

/*
Rutas generales
*/
$router->get('/', fn() => $makePageCtrl()->home());
$router->get('/catalog', fn() => $makeCatalogCtrl()->index());
$router->get('/detalle_pelicula', fn() => $makePageCtrl()->home()); // dead route, keep for reference
$router->get('/movie', fn() => $makeMovieCtrl()->show());

/*
Rutas de usuario
*/
$router->get('/profile', $protegida(fn() => $makeUserCtrl()->profile()));
$router->get('/login', fn() => $makeUserCtrl()->login());
$router->post('/login', fn() => $makeUserCtrl()->handleLogin());
$router->post('/logout', fn() => $makeUserCtrl()->logout());
$router->get('/register', fn() => $makeUserCtrl()->register());
$router->post('/register', fn() => $makeUserCtrl()->handleRegister());
$router->get('/profile/edit', $protegida(fn() => $makeUserCtrl()->editProfile()));
$router->post('/profile/edit', $protegida(fn() => $makeUserCtrl()->updateProfile()));

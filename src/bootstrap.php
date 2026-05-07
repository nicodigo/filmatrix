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
use App\Repository\TitleRepository;
use App\Repository\GenreRepository;
use App\Repository\PeopleRepository;
use App\Repository\CatalogListRepository;
use App\Repository\ReviewRepository;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Services\CatalogSyncService;

use App\Middleware\AuthMiddleware;

use App\Infrastructure\Tmdb\TmdbClient;

use App\Controllers\PageController;
use App\Controllers\CatalogController;
use App\Controllers\MovieController;
use App\Controllers\UserController;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

$config = new Config();

/*
|--------------------------------------------------------------------------
| Logger
|--------------------------------------------------------------------------
*/
$log_app = new Logger('log-app');
$handler = new StreamHandler($config->get('LOG_PATH'));
$handler->setLevel($config->get('LOG_LEVEL'));
$log_app->pushHandler($handler);

/*
|--------------------------------------------------------------------------
| DB
|--------------------------------------------------------------------------
*/
$connectionBuilder = new ConnectionBuilder();
$connectionBuilder->setLogger($log_app);
$connection = $connectionBuilder->make($config);

/*
|--------------------------------------------------------------------------
| Repositories
|--------------------------------------------------------------------------
*/
$userRepository        = new UserRepository($connection);
$titleRepository       = new TitleRepository($connection);
$genreRepository       = new GenreRepository($connection);
$peopleRepository      = new PeopleRepository($connection);
$catalogListRepository = new CatalogListRepository($connection);
$reviewRepository      = new ReviewRepository($connection);

/*
|--------------------------------------------------------------------------
| External clients
|--------------------------------------------------------------------------
*/
$tmdbClient = new TmdbClient($config);

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/
$authService   = new AuthService($userRepository, $log_app);
$userService   = new UserService($userRepository);
$genreService  = new GenreService($genreRepository);
$peopleService = new PeopleService($peopleRepository);
$reviewService = new ReviewService($reviewRepository);

$titleService = new TitleService(
    $titleRepository,
    $genreService,
    $peopleService,
    $tmdbClient,
    $config,
    $log_app,
    $catalogListRepository
);

$catalogSyncService = new CatalogSyncService(
    $titleService,
    $catalogListRepository,
    $tmdbClient,
    $log_app
);

/*
|--------------------------------------------------------------------------
| Middleware
|--------------------------------------------------------------------------
*/
$authMiddleware = new AuthMiddleware();

/*
|--------------------------------------------------------------------------
| Request
|--------------------------------------------------------------------------
*/
$request = new Request();

/*
|--------------------------------------------------------------------------
| Controllers factories
|--------------------------------------------------------------------------
*/
$makeUserCtrl = fn() => new UserController($authService, $userService);

$makePageCtrl = fn() => new PageController($catalogListRepository);

$makeCatalogCtrl = fn() => new CatalogController(
    $catalogSyncService
);

$makeMovieCtrl = fn() => new MovieController(
    $titleService,
    $reviewService,
    $catalogSyncService,
    $genreService,
    $peopleService
);

/*
|--------------------------------------------------------------------------
| Protected helper
|--------------------------------------------------------------------------
*/
$protegida = fn(callable $action) => function() use ($authMiddleware, $action) {
    $authMiddleware->handle();
    return $action();
};

/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/
$router = new Router();
$router->setLogger($log_app);

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
*/
$router->get('/', fn() => $makePageCtrl()->home());

$router->get('/catalog', fn() => $makeCatalogCtrl()->index());

$router->get('/movie', fn() => $makeMovieCtrl()->show());

/*
|--------------------------------------------------------------------------
| User routes
|--------------------------------------------------------------------------
*/
$router->get('/profile', $protegida(fn() => $makeUserCtrl()->profile()));
$router->get('/login', fn() => $makeUserCtrl()->login());
$router->post('/login', fn() => $makeUserCtrl()->handleLogin());
$router->post('/logout', fn() => $makeUserCtrl()->logout());
$router->get('/register', fn() => $makeUserCtrl()->register());
$router->post('/register', fn() => $makeUserCtrl()->handleRegister());
$router->get('/profile/edit', $protegida(fn() => $makeUserCtrl()->editProfile()));
$router->post('/profile/edit', $protegida(fn() => $makeUserCtrl()->updateProfile()));
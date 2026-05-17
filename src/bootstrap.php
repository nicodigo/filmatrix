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
use App\Repository\FilmListRepository;
use App\Repository\ReviewRepository;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Services\FilmSyncService;

use App\Middleware\AuthMiddleware;

use App\Infrastructure\Tmdb\TmdbClient;

use App\Controllers\PageController;
use App\Controllers\FilmController;
use App\Controllers\MovieController;
use App\Controllers\ReviewController;
use App\Controllers\UserController;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$config = new Config();

// Logger
$log_app = new Logger('log-app');
$handler = new StreamHandler('php://stderr');
$handler->setLevel($config->get('LOG_LEVEL'));
$log_app->pushHandler($handler);

// DB
$connectionBuilder = new ConnectionBuilder();
$connectionBuilder->setLogger($log_app);
$connection = $connectionBuilder->make($config);

// twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');

$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/../cache/twig',
    'auto_reload' => true,  // recompila si la vista cambió
    'debug' => true,        // false en producción
]);

session_name('FILMATRIX_SESSION');

session_set_cookie_params([
    'lifetime' => (int)$config->get('SESSION_LIFETIME'), // Expira al cerrar navegador
    'path' => '/',                      // Toda la app
    'domain' => '',                     // Dominio actual
    'secure' => $_ENV['APP_ENV'] === 'production', // Solo HTTPS en producción
    'httponly' => true,                 // No accesible desde JS
    'samesite' => 'Lax',                // Balance seguridad/usabilidad
]);

session_start();

// Repositories
$userRepository        = new UserRepository($connection);
$titleRepository       = new TitleRepository($connection);
$genreRepository       = new GenreRepository($connection);
$peopleRepository      = new PeopleRepository($connection);
$filmListRepository = new FilmListRepository($connection);
$reviewRepository      = new ReviewRepository($connection);

// External clients
$tmdbClient = new TmdbClient($config);

// Services
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
);

$filmSyncService = new FilmSyncService(
    $titleService,
    $filmListRepository,
    $tmdbClient,
    $log_app
);

// Middleware
$authMiddleware = new AuthMiddleware();

// Request
$request = new Request();

// Controllers factories
$makeUserCtrl = fn() => new UserController($twig, $authService, $userService, $request);

$makePageCtrl = fn() => new PageController($twig, $filmListRepository, $request);

$makeFilmCtrl = fn() => new FilmController(
    $twig,
    $filmSyncService,
    $request
);

$makeMovieCtrl = fn() => new MovieController(
    $twig,
    $titleService,
    $reviewService,
    $filmSyncService,
    $genreService,
    $peopleService,
    $request
);

$makeReviewCtrl = fn() => new ReviewController($twig, $reviewService, $request);

// Protected helper
$protegida = fn(callable $action) => function () use ($authMiddleware, $action) {
    $authMiddleware->handle();
    return $action();
};

// Router
$router = new Router($twig);
$router->setLogger($log_app);

// Routes
$router->get('/', fn() => $makePageCtrl()->home());
$router->get('/films', fn() => $makeFilmCtrl()->index());
$router->get('/movie', fn() => $makeMovieCtrl()->showMovie());


$router->get('/profile', $protegida(fn() => $makeUserCtrl()->profile()));
$router->get('/login', fn() => $makeUserCtrl()->login());
$router->post('/login', fn() => $makeUserCtrl()->handleLogin());
$router->post('/logout', fn() => $makeUserCtrl()->logout());
$router->get('/register', fn() => $makeUserCtrl()->register());
$router->post('/register', fn() => $makeUserCtrl()->handleRegister());
$router->get('/profile/edit', $protegida(fn() => $makeUserCtrl()->editProfile()));
$router->post('/profile/edit', $protegida(fn() => $makeUserCtrl()->updateProfile()));
$router->post('/review/post', $protegida(fn() => $makeReviewCtrl()->postReview()));

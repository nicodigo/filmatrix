<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\WatchlistController;
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
use App\Repository\TitleListRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserListRepository;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Services\UserListService;

use App\Middleware\AuthMiddleware;

use App\Infrastructure\Tmdb\TmdbClient;

use App\Controllers\PageController;
use App\Controllers\TitleController;
use App\Controllers\ReviewController;
use App\Controllers\UserController;
use App\Controllers\UserListController;
use App\Repository\WatchlistRepository;
use App\Services\WatchlistService;
use App\Services\TitleListService;

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


session_name('FILMATRIX_SESSION');

session_set_cookie_params([
    'lifetime' => (int)$config->get('SESSION_LIFETIME'),
    'path' => '/',
    'domain' => '',
    'secure' => $_ENV['APP_ENV'] === 'production',
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');

$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/../cache/twig',
    'auto_reload' => true,
    'debug' => true,
]);

$twig->addGlobal('csrf_token', $_SESSION['csrf_token']);

$twig->addGlobal('app', [
    'user_logged_in' => !empty($_SESSION['user_id']),
    'username' => $_SESSION['username'] ?? null,
    'user_role' => $_SESSION['user_role'] ?? null,
]);

// Repositories
$userRepository         = new UserRepository($connection);
$titleRepository        = new TitleRepository($connection);
$genreRepository        = new GenreRepository($connection);
$peopleRepository       = new PeopleRepository($connection);
$titleListRepository    = new TitleListRepository($connection);
$reviewRepository       = new ReviewRepository($connection);
$watchlistRepository    = new WatchlistRepository($connection);
$userListRepository     = new UserListRepository($connection);

// External clients
$tmdbClient = new TmdbClient($config);
$tmdbClient->setLogger($log_app);

// Services
$authService    = new AuthService($userRepository, $log_app);
$userService    = new UserService($userRepository);
$genreService   = new GenreService($genreRepository);
$peopleService  = new PeopleService($peopleRepository);

$titleService = new TitleService(
    $titleRepository,
    $genreService,
    $peopleService,
    $tmdbClient,
    $log_app,
);

$titleListService = new TitleListService(
    $titleService,
    $titleListRepository,
    $tmdbClient,
    $log_app
);

$watchlistService = new WatchlistService($watchlistRepository, $titleService);
$reviewService   = new ReviewService($reviewRepository, $watchlistService, $log_app);
$userListService = new UserListService($userListRepository);


// ─── Sincronizar géneros al arrancar ───────────────────────────────────────
try {
    $genresData = $tmdbClient->getGenres();
    foreach ($genresData['genres'] ?? [] as $genre) {
        $genreService->sync((int) $genre['id'], (string) $genre['name']);
    }
    $log_app->info('Géneros sincronizados correctamente desde TMDB.');
} catch (\Throwable $e) {
    $log_app->error('Error al sincronizar géneros desde TMDB: ' . $e->getMessage());
}
// ──────────────────────────────────────────────────────────────────────────


// Middleware
$authMiddleware = new AuthMiddleware();

// Request
$request = new Request();

// Controllers factories
$makeUserCtrl = fn() => new UserController($twig, $authService, $userService, $request);

$makePageCtrl = fn() => new PageController($twig, $titleListRepository, $request);

$makeTitleCtrl = fn() => new TitleController(
    $twig,
    $titleListService,
    $titleService,
    $reviewService,
    $genreService,
    $peopleService,
    $watchlistService,
    $request
);

$makeReviewCtrl = fn() => new ReviewController($twig, $reviewService, $request);

$makeWatchlistCtrl = fn() => new WatchlistController($watchlistService, $twig, $request);

$makeUserListCtrl = fn() => new UserListController($userListService, $twig, $request);

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
$router->get('/titles', fn() => $makeTitleCtrl()->index());
$router->get('/titles/search', fn() => $makeTitleCtrl()->search());
$router->get('/titles/detail', fn() => $makeTitleCtrl()->show());

$router->get('/profile', $protegida(fn() => $makeUserCtrl()->profile()));
$router->get('/login', fn() => $makeUserCtrl()->login());
$router->post('/login', fn() => $makeUserCtrl()->handleLogin());
$router->post('/logout', fn() => $makeUserCtrl()->logout());
$router->get('/register', fn() => $makeUserCtrl()->register());
$router->post('/register', fn() => $makeUserCtrl()->handleRegister());
$router->get('/profile/edit', $protegida(fn() => $makeUserCtrl()->editProfile()));
$router->post('/profile/edit', $protegida(fn() => $makeUserCtrl()->updateProfile()));
$router->post('/profile/password', $protegida(fn() => $makeUserCtrl()->updatePassword()));
$router->get('/profile/password', $protegida(fn() => $makeUserCtrl()->getUpdatePassword()));
$router->post('/review/post', $protegida(fn() => $makeReviewCtrl()->postReview()));
$router->post('/review/update', $protegida(fn() => $makeReviewCtrl()->update()));
$router->post('/review/delete', $protegida(fn() => $makeReviewCtrl()->delete()));
$router->get('/my-reviews', $protegida(fn() => $makeUserCtrl()->myReviews()));

$router->get('/my-watchlist', $protegida(fn() => $makeWatchlistCtrl()->index()));
$router->post('/my-watchlist', $protegida(fn() => $makeWatchlistCtrl()->store()));
$router->patch('/my-watchlist', $protegida(fn() => $makeWatchlistCtrl()->update()));
$router->delete('/my-watchlist', $protegida(fn() => $makeWatchlistCtrl()->delete()));

$router->get('/my-lists', $protegida(fn() => $makeUserListCtrl()->index()));
$router->post('/my-lists', $protegida(fn() => $makeUserListCtrl()->store()));
$router->patch('/my-lists', $protegida(fn() => $makeUserListCtrl()->update()));
$router->delete('/my-lists', $protegida(fn() => $makeUserListCtrl()->delete()));
$router->get('/my-lists/detail', $protegida(fn() => $makeUserListCtrl()->show()));
$router->post('/my-lists/item', $protegida(fn() => $makeUserListCtrl()->addItem()));
$router->delete('/my-lists/item', $protegida(fn() => $makeUserListCtrl()->removeItem()));
$router->get('/my-lists/available', $protegida(fn() => $makeUserListCtrl()->available()));

$router->get('/acerca-de-nosotros', fn() => $makePageCtrl()->about());
$router->get('/contacto', fn() => $makePageCtrl()->contact());

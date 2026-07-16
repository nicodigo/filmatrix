<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AdminReviewController;
use App\Controllers\AdminHeroController;
use App\Controllers\Api\AuthTokenController;
use App\Controllers\Api\ReviewApiController;
use App\Controllers\Api\WatchlistApiController;
use App\Controllers\RecommendationController;
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
use App\Repository\GenrePreferenceRepository;
use App\Repository\RecommendationRepository;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\GenreService;
use App\Services\PeopleService;
use App\Services\ReviewService;
use App\Services\TitleService;
use App\Services\UserListService;
use App\Services\GenrePreferenceService;
use App\Services\RecommendationService;

use App\Repository\ReviewReportRepository;

use App\Middleware\AuthMiddleware;

use App\Infrastructure\Tmdb\TmdbClient;

use App\Controllers\PageController;
use App\Controllers\TitleController;
use App\Controllers\ReviewController;
use App\Controllers\UserController;
use App\Controllers\UserListController;
use App\Controllers\UpcomingReleaseController;
use App\Repository\WatchlistRepository;
use App\Services\WatchlistService;
use App\Services\TitleListService;

use App\Controllers\SitemapController;
use App\Core\Exceptions\InvalidApiTokenException;
use App\Core\Exceptions\InvalidCredentialsException;
use App\Core\Http\ApiResponse;
use App\Middleware\AdminMiddleware;
use App\Middleware\ApiAuthMiddleware;
use App\Repository\ApiTokenRepository;
use App\Repository\LoginAttemptRepository;
use App\Services\ApiTokenService;

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

// Request
$request = new Request();

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
    'role' => $_SESSION['role'] ?? null,
]);

$twig->addGlobal('app_url', rtrim($config->get('APP_URL'), '/'));
$twig->addGlobal('current_path', $request->uri());

// Repositories
$userRepository             = new UserRepository($connection);
$titleRepository            = new TitleRepository($connection);
$genreRepository            = new GenreRepository($connection);
$peopleRepository           = new PeopleRepository($connection);
$titleListRepository        = new TitleListRepository($connection);
$reviewRepository           = new ReviewRepository($connection);
$loginAttemptRepository     = new LoginAttemptRepository($connection);
$watchlistRepository        = new WatchlistRepository($connection);
$userListRepository         = new UserListRepository($connection);
$genrePreferenceRepository  = new GenrePreferenceRepository($connection);
$recommendationRepository   = new RecommendationRepository($connection);

$apiTokenRepository = new ApiTokenRepository($connection);

// External clients
$tmdbClient = new TmdbClient($config);
$tmdbClient->setLogger($log_app);

// Services
$authService    = new AuthService(
    $userRepository,
    $log_app,
    $loginAttemptRepository,
    (int) $config->get('LOGIN_MAX_ATTEMPTS'),
    (int) $config->get('LOGIN_LOCKOUT_WINDOW_SECONDS')
);
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

// GenrePreferenceService: requiere TitleRepository para buscar géneros por título.
$genrePreferenceService = new GenrePreferenceService(
    $genrePreferenceRepository,
    $titleRepository,
);

// WatchlistService: recibe GenrePreferenceService para actualizar pesos al marcar 'watched'.
$watchlistService = new WatchlistService(
    $watchlistRepository,
    $titleService,
    $genrePreferenceService,
);

// ReviewService: recibe GenrePreferenceService para actualizar pesos al reseñar.
$reviewReportRepository = new ReviewReportRepository($connection);
$reviewService = new ReviewService(
    $reviewRepository,
    $watchlistService,
    $log_app,
    $genrePreferenceService,
    $reviewReportRepository,
);

$userListService = new UserListService($userListRepository);

// RecommendationService: ya no recibe WatchlistRepository — discard()/isDiscarded()
// se movieron a RecommendationRepository (no tienen relación con watchlist_items).
$recommendationService = new RecommendationService(
    $recommendationRepository,
    $genrePreferenceService,
    $genreService,
);

// --- Api Services ---
$apiTokenService = new ApiTokenService($apiTokenRepository);

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


// Middlewares
$authMiddleware = new AuthMiddleware();
$adminMiddleware = new AdminMiddleware();

$apiAuthMiddleware = new ApiAuthMiddleware($apiTokenService);

// Controllers factories
$makeUserCtrl = fn() => new UserController(
    $twig,
    $authService,
    $userService,
    $request,
    $config->get('TRUSTED_PROXY_CIDRS')
);

$makePageCtrl = fn() => new PageController($twig, $titleListRepository, $reviewRepository, $request);

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

$makeRecommendationCtrl = fn() => new RecommendationController(
    $recommendationService,
    $twig,
    $request,
);

$makeUpcomingCtrl = fn() => new UpcomingReleaseController($titleListService, $twig, $request);

$makeAdminReviewCtrl = fn() => new AdminReviewController($twig, $reviewService, $request);

$makeAdminHeroCtrl = fn() => new AdminHeroController($twig, $request);

$makeAuthTokenCtrl = fn() => new AuthTokenController($apiTokenService, $userRepository, $request);

$makeReviewApiCtrl = fn() => new ReviewApiController($reviewService, $reviewRepository, $request);

$makeWatchlistApiCtrl = fn() => new WatchlistApiController($watchlistService, $request);

// Protected helper
$protegida = fn(callable $action) => function () use ($authMiddleware, $action) {
    $authMiddleware->handle();
    return $action();
};

$esAdmin = fn(callable $action) => function () use ($adminMiddleware, $action) {
    $adminMiddleware->handle();
    return $action();
};

$apiProtegida = fn(callable $action) => function () use ($apiAuthMiddleware, $action, $request) {
    try {
        $userId = $apiAuthMiddleware->authenticate($request);
    } catch (InvalidApiTokenException $e) {
        ApiResponse::error(401, $e->getMessage());
        return;
    }
    return $action($userId);
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
$router->post('/review/report', $protegida(fn() => $makeReviewCtrl()->report()));
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

$router->get('/recommendations', $protegida(fn() => $makeRecommendationCtrl()->index()));
$router->post('/recommendations/discard', $protegida(fn() => $makeRecommendationCtrl()->discard()));

$router->get('/acerca-de-nosotros', fn() => $makePageCtrl()->about());
$router->get('/contacto', fn() => $makePageCtrl()->contact());

$router->get('/upcoming', fn() => $makeUpcomingCtrl()->index());


$makeSitemapCtrl = fn() => new SitemapController($twig, $titleService);
$router->get('/sitemap.xml', fn() => $makeSitemapCtrl()->index());

// Ruta pública para servir la imagen del hero (desde PVC o fallback)
$router->get('/assets/hero-image', fn() => $makeAdminHeroCtrl()->serve());


// Rutas de admin
$router->get('/admin/reviews', $esAdmin(fn() => $makeAdminReviewCtrl()->index()));
$router->post('/admin/reviews/hide', $esAdmin(fn() => $makeAdminReviewCtrl()->hide()));
$router->post('/admin/reviews/show', $esAdmin(fn() => $makeAdminReviewCtrl()->show()));
$router->post('/admin/reviews/unflag', $esAdmin(fn() => $makeAdminReviewCtrl()->unflag()));
$router->post('/admin/reviews/delete', $esAdmin(fn() => $makeAdminReviewCtrl()->delete()));

// Hero image admin
$router->get('/admin/hero', $esAdmin(fn() => $makeAdminHeroCtrl()->index()));
$router->post('/admin/hero/upload', $esAdmin(fn() => $makeAdminHeroCtrl()->upload()));
$router->post('/admin/hero/reset', $esAdmin(fn() => $makeAdminHeroCtrl()->reset()));


// ─── API v1 ─────────────────────────────────────────────────────────────
$router->get('/api/v1', function () {
    ApiResponse::json([
        'links' => \App\Core\Http\Links::build([
            'auth-tokens' => ['href' => '/api/v1/auth/tokens', 'method' => 'POST'],
            'reviews'     => ['href' => '/api/v1/reviews', 'method' => 'GET'],
            'watchlist'   => ['href' => '/api/v1/watchlist', 'method' => 'GET'],
        ]),
    ]);
});

$router->post('/api/v1/auth/tokens', function () use ($makeAuthTokenCtrl) {
    try {
        $makeAuthTokenCtrl()->store();
    } catch (InvalidCredentialsException $e) {
        ApiResponse::error(401, $e->getMessage());
    }
});
$router->get('/api/v1/auth/tokens', $apiProtegida(fn($userId) => $makeAuthTokenCtrl()->index($userId)));
$router->delete('/api/v1/auth/tokens', $apiProtegida(fn($userId) => $makeAuthTokenCtrl()->destroy($userId)));


$router->get('/api/v1/reviews', $apiProtegida(function ($userId) use ($makeReviewApiCtrl, $request) {
    $ctrl = $makeReviewApiCtrl();
    $request->get('id') !== null ? $ctrl->show($userId) : $ctrl->index($userId);
}));
$router->post('/api/v1/reviews', $apiProtegida(fn($userId) => $makeReviewApiCtrl()->store($userId)));
$router->patch('/api/v1/reviews', $apiProtegida(fn($userId) => $makeReviewApiCtrl()->update($userId)));
$router->delete('/api/v1/reviews', $apiProtegida(fn($userId) => $makeReviewApiCtrl()->destroy($userId)));

$router->get('/api/v1/watchlist', $apiProtegida(function ($userId) use ($makeWatchlistApiCtrl, $request) {
    $ctrl = $makeWatchlistApiCtrl();
    $request->get('title_id') !== null ? $ctrl->show($userId) : $ctrl->index($userId);
}));
$router->post('/api/v1/watchlist', $apiProtegida(fn($userId) => $makeWatchlistApiCtrl()->store($userId)));
$router->patch('/api/v1/watchlist', $apiProtegida(fn($userId) => $makeWatchlistApiCtrl()->update($userId)));
$router->delete('/api/v1/watchlist', $apiProtegida(fn($userId) => $makeWatchlistApiCtrl()->destroy($userId)));

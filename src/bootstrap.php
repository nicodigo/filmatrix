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
use App\Core\TmdbClient;
use App\Repository\TitleRepository;
use App\Repository\GenreRepository;
use App\Repository\PeopleRepository;
use App\Repository\CatalogListRepository;
use App\Services\TitleService;
use App\Controllers\CatalogoController;
use App\Repository\ReviewRepository;
use App\Controllers\DetalleController;

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
    $log_app,
    $catalogListRepository
);

$authMiddleware = new AuthMiddleware();

$request = new Request();

// Factories de controllers
$makeUserCtrl = fn() => new \App\Controllers\UserController($authService, $userService);
$makePageCtrl = fn() => new \App\Controllers\PageController($catalogListRepository);
$makeCatalogoCtrl = fn() => new CatalogoController($titleService, $catalogListRepository);
$makeDetalleCtrl = fn() => new DetalleController(
    $titleService,
    $titleRepository,
    $reviewRepository,
    $catalogListRepository
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
$router->get('/catalogo', fn() => $makeCatalogoCtrl()->index());
$router->get('/detalle_pelicula', fn() => $makePageCtrl()->detalle_pelicula());
$router->get('/pelicula', fn() => $makeDetalleCtrl()->show());

/*
Rutas de usuario
*/
$router->get('/perfil', $protegida(fn() => $makeUserCtrl()->perfil()));
$router->get('/login', fn() => $makeUserCtrl()->login());
$router->post('/login', fn() => $makeUserCtrl()->hacerLogin());
$router->post('/logout', fn() => $makeUserCtrl()->logout());
$router->get('/registro', fn() => $makeUserCtrl()->registro());
$router->post('/registro', fn() => $makeUserCtrl()->hacerRegistro());
$router->get('/perfil/editar', $protegida(fn() => $makeUserCtrl()->editarPerfil()));
$router->post('/perfil/editar', $protegida(fn() => $makeUserCtrl()->guardarPerfil()));

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
$authMiddleware = new AuthMiddleware();

$request = new Request();

// Factories de controllers
$makeUserCtrl = fn() => new \App\Controllers\UserController($authService, $userService);
$makePageCtrl = fn() => new \App\Controllers\PageController();

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
$router->get('/catalogo', fn() => $makePageCtrl()->catalogo());
$router->get('/detalle_pelicula', fn() => $makePageCtrl()->detalle_pelicula());

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

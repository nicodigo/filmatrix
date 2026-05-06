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
$authMiddleware = new AuthMiddleware();

$request = new Request();

$router = new Router();
$router->setLogger($log_app);

/*
Rutas generales
*/
$router->get('/', function () {
    $controller = new \App\Controllers\PageController();
    $controller->home();
});

$router->get('/catalogo', function () {
    $controller = new \App\Controllers\PageController();
    $controller->catalogo();
});

$router->get('/detalle_pelicula', function () {
    $controller = new \App\Controllers\PageController();
    $controller->detalle_pelicula();
});

/*
Rutas de usuario
*/
$router->get('/perfil', function () use ($authMiddleware, $authService, $userRepository) {
    $authMiddleware->handle();
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->perfil();
});

$router->get('/login', function () use ($authService, $userRepository) {
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->login();
});

$router->post('/login', function () use ($authService, $userRepository) {
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->hacerLogin();
});

$router->post('/logout', function () use ($authService, $userRepository) {
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->logout();
});

$router->get('/registro', function () use ($authService, $userRepository) {
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->registro();
});

$router->post('/registro', function () use ($authService, $userRepository) {
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->hacerRegistro();
});

$router->get('/perfil/editar', function () use ($authMiddleware, $authService, $userRepository) {
    $authMiddleware->handle();
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->editarPerfil();
});

$router->post('/perfil/editar', function () use ($authMiddleware, $authService, $userRepository) {
    $authMiddleware->handle();
    $controller = new \App\Controllers\UserController($authService, $userRepository);
    $controller->guardarPerfil();
});
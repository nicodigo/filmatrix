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

$connectionBuilder = new ConnectionBuilder;
$connectionBuilder->setLogger($log_app);
$connection = $connectionBuilder->make($config);

$userRepository = new UserRepository($connection);
$authService = new AuthService($userRepository, $log_app);
$authMiddleware = new AuthMiddleware();

$request = new Request;

$router = new Router();
$router->setLogger($log_app);

$router->get('/', function() {
    $controller = new \App\Controllers\PageController();
    $controller->home();
});
$router->get('/catalogo', function(){
    $controller = new \App\Controllers\PageController();
    $controller->catalogo();
});
$router->get('/detalle_pelicula', function(){
    $controller = new \App\Controllers\PageController();
    $controller->detalle_pelicula();
});
$router->get('/perfil', function() use ($authMiddleware, $authService) {
    $authMiddleware->handle();
    $controller = new \App\Controllers\UserController($authService);
    $controller->perfil();
});
$router->get('/login', function() use ($authService) {
    $controller = new \App\Controllers\UserController($authService);
    $controller->login();
});
$router->post('/login', function() use ($authService) {
    $controller = new \App\Controllers\UserController($authService);
    $controller->hacerLogin();
});
$router->post('/logout', function() use ($authService) {
    $controller = new \App\Controllers\UserController($authService);
    $controller->logout();
});
$router->get('/registro', function() use ($authService) {
    $controller = new \App\Controllers\UserController($authService);
    $controller->registro();
});
$router->post('/registro', function() use ($authService) {
    $controller = new \App\Controllers\UserController($authService);
    $controller->hacerRegistro();
});
$router->get('/perfil/editar', function() use ($authMiddleware, $authService) {
    $authMiddleware->handle();
    $controller = new \App\Controllers\UserController($authService);
    $controller->editarPerfil();
});
$router->post('/perfil/editar', function() use ($authMiddleware, $authService) {
    $authMiddleware->handle();
    $controller = new \App\Controllers\UserController($authService);
    $controller->guardarPerfil();
});

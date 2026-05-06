<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Dotenv\Dotenv;

use App\Core\Router;
use App\Core\Config;
use App\Core\Database\ConnectionBuilder;
use App\Core\Request;

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

$request = new Request;

$router = new Router($log_app);
$router->setLogger($log_app);

$router->get('/', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\PageController();
    $controller->home();
});
$router->get('/catalogo', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\PageController();
    $controller->catalogo();
});
$router->get('/detalle_pelicula', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\PageController();
    $controller->detalle_pelicula();
});
$router->get('/perfil', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->perfil();
});
$router->get('/login', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->login();
});
$router->post('/login', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->hacerLogin();
});
$router->post('/logout', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->logout();
});
$router->get('/registro', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->registro();
});
$router->post('/registro', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->hacerRegistro();
});
$router->get('/perfil/editar', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->editarPerfil();
});
$router->post('/perfil/editar', function() use ($connection, $log_app) {
    $controller = new \App\Controllers\UserController();
    $controller->guardarPerfil();
});

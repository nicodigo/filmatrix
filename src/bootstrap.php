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

$router->get('/', 'PageController@home');
$router->get('/catalogo', 'PageController@catalogo');
$router->get('/detalle_pelicula', 'PageController@detalle_pelicula');
$router->get('/perfil', 'UserController@perfil');
$router->get('/login', 'UserController@login');
$router->post('/login', 'UserController@hacerLogin');
$router->post('/logout', 'UserController@logout');
$router->get('/registro', 'UserController@registro');
$router->post('/registro', 'UserController@hacerRegistro');
$router->get('/perfil/editar', 'UserController@editarPerfil');
$router->post('/perfil/editar', 'UserController@guardarPerfil');

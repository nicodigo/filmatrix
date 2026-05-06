<?php

use App\Core\Exceptions\RouteNotFoundException;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../src/bootstrap.php';

$router->dispatch($request);
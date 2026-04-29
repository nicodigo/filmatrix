<?php

/* echo "<pre>"; */
/* print_r($_SERVER); */
/* die; */

use App\Core\Exceptions\RouteNotFoundException;

require __DIR__ . '/../src/bootstrap.php';

$router->dispatch($request);

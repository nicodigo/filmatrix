<?php

namespace App\Core;

require __DIR__ . '/../../vendor/autoload.php';

use Exception;

use App\Core\Exceptions\RouteNotFoundException;
use App\Core\Request;
use App\Core\Traits\Loggable;

class Router
{
    use Loggable;

    public array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public string $notFound = 'not_found';
    public string $internalError = 'internal_error';

    public function __construct()
    {
        $this->get($this->notFound, function() {
            $controller = new \App\Controllers\ErrorController();
            $controller->notFound();
        });
        $this->get($this->internalError, function() {
            $controller = new \App\Controllers\ErrorController();
            $controller->internalError();
        });
    }

    public function dispatch(Request $request): void
    {
        $action = null;
        try {
            list($path, $http_method) = $request->route();
            $this->logger
                ->info(
                    'Petición entrante',
                    [
                        'Path' => $path,
                        'Method' => $http_method,
                    ]
                );

            $action = $this->getAction($path, $http_method);
            $this->logger
                ->info(
                    'Status Code: 200 OK',
                    [
                        'Path' => $path,
                        'Method' => $http_method,
                    ]
                );
        } catch (RouteNotFoundException $e) {
            $action = $this->getAction($this->notFound, 'GET');
            $this->logger->debug(
                'Status Code: 404 - Route Not Found',
                ['ERROR' => $e],
            );
        } catch (Exception $e) {
            $action = $this->getAction($this->internalError, 'GET');
            $this->logger->error(
                'Status Code: 500 - Internal Server Error',
                ['ERROR' => $e],
            );
        } finally {
            $this->call($action);
        }
    }

    public function getAction($path, $http_method): callable
    {
        if (!$this->routeExists($path, $http_method)) {
            throw new RouteNotFoundException('No existe ruta para este Path');
        }
        return $this->routes[$http_method][$path];
    }

    public function call(callable $action): void
    {
        $action();
    }

    public function loadRoutes($path, callable $action, $http_method = 'GET'): void
    {
        $this->routes[$http_method][$path] = $action;
    }

    public function get($path, callable $action)
    {
        $this->loadRoutes($path, $action, 'GET');
    }

    public function post($path, callable $action)
    {
        $this->loadRoutes($path, $action, 'POST');
    }

    public function routeExists($path, $http_method): bool
    {
        return (array_key_exists($path, $this->routes[$http_method]));
    }
}

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
        $this->get($this->notFound, 'ErrorController@notFound');
        $this->get($this->internalError, 'ErrorController@internalError');
    }

    public function dispatch(Request $request): void
    {

        try {
            list($path, $http_method) = $request->route();

            list($controllerName, $method) = $this->getController($path, $http_method);
            $this->logger
                ->info(
                    'Status Code: 200 OK',
                    [
                        'Path' => $path,
                        'Method' => $http_method,
                    ]
                );
        } catch (RouteNotFoundException $e) {

            list($controllerName, $method) = $this->getController($this->notFound, 'GET');
            $this->logger->debug(
                'Status Code: 404 - Route Not Found',
                ['ERROR' => $e],
            );
        } catch (Exception $e) {

            list($controllerName, $method) = $this->getController($this->internalError, 'GET');
            $this->logger->debug(
                'Status Code: 500 - Internal Server Error',
                ['ERROR' => $e],
            );
        } finally {
            $this->call($controllerName, $method);
        }
    }

    public function getController($path, $http_method)
    {
        if (!$this->routeExists($path, $http_method)) {
            throw new RouteNotFoundException('No existe ruta para este Path');
        }
        return explode('@', $this->routes[$http_method][$path]);
    }

    public function call($controllerName, $method)
    {
        $controller = new ("App\\Controllers\\{$controllerName}");
        $controller->$method();
    }

    public function loadRoutes($path, $action, $http_method = 'GET'): void
    {
        $this->routes[$http_method][$path] = $action;
    }

    public function get($path, $action)
    {
        $this->loadRoutes($path, $action, 'GET');
    }

    public function post($path, $action)
    {
        $this->loadRoutes($path, $action, 'POST');
    }

    public function routeExists($path, $http_method): bool
    {
        return (array_key_exists($path, $this->routes[$http_method]));
    }
}

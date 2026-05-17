<?php

namespace App\Core;

class Request
{

    public function uri()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function route()
    {
        return [
            $this->uri(),
            $this->method(),
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function session(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function setSession(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function unsetSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}

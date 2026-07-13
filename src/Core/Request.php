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

    public function setFlash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function clientIp(array $trustedProxyCidrs = []): string
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (!$this->ipMatchesAny($remoteAddr, $trustedProxyCidrs)) {
            return $remoteAddr;
        }

        $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;

        if ($forwardedFor) {
            $ips = array_map('trim', explode(',', $forwardedFor));
            return $ips[0];
        }

        return $_SERVER['HTTP_X_REAL_IP'] ?? $remoteAddr;
    }

    private function ipMatchesAny(string $ip, array $cidrs): bool
    {
        foreach ($cidrs as $cidr) {
            if ($this->ipInCidr($ip, trim($cidr))) {
                return true;
            }
        }

        return false;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $bits] = explode('/', $cidr);
        $bits = (int) $bits;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = -1 << (32 - $bits);
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}

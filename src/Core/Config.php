<?php

namespace App\Core;

class Config
{

    private array $configs;

    public function __construct()
    {
        $this->configs['LOG_LEVEL'] = getenv('LOG_LEVEL') ?: 'INFO';
        $this->configs['LOG_PATH'] = 'php://stderr';

        $this->configs['DB_ADAPTER'] = getenv('DB_ADAPTER') ?: 'pgsql';
        $this->configs['DB_HOSTNAME'] = getenv('DB_HOSTNAME') ?: 'localhost';
        $this->configs['DB_DBNAME'] = getenv('DB_DBNAME') ?: 'pawprints_db';
        $this->configs['DB_USERNAME'] = getenv('DB_USERNAME') ?: '';
        $this->configs['DB_PASSWORD'] = getenv('DB_PASSWORD') ?: '';
        $this->configs['DB_PORT'] = getenv('DB_PORT') ?: '5432';
        $this->configs['DB_CHARSET'] = getenv('DB_CHARSET') ?: 'utf8';
        $this->configs['TMDB_CACHE_TTL_DAYS'] = getenv('TMDB_CACHE_TTL_DAYS') ?: 30;

        $this->configs['TMDB_READ_ACCESS_TOKEN'] = getenv('TMDB_READ_ACCESS_TOKEN') ?: '';

        $this->configs['SESSION_LIFETIME'] = getenv('SESSION_LIFETIME') ?: 0;
        $this->configs['SESSION_GC_MAXLIFETIME'] = getenv('SESSION_GC_MAXLIFETIME') ?: 86400;
        $this->configs['APP_URL'] = getenv('APP_URL') ?: 'http://localhost:8000';
    }

    public function get($name)
    {
        return $this->configs[$name] ?? null;
    }
}

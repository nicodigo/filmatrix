<?php

namespace App\Core\Database;

use PDO;
use PDOException;

use App\Core\Config;
use App\Core\Traits\Loggable;
use Exception;

class ConnectionBuilder
{

    use Loggable;

    public function make(Config $config): PDO
    {
        try {
            $adapter = $config->get('DB_ADAPTER');
            $hostname = $config->get('DB_HOSTNAME');
            $dbname = $config->get('DB_DBNAME');
            $port = $config->get('DB_PORT');
            $charset = $config->get('DB_CHARSET');

            return new PDO(
                "{$adapter}:host={$hostname};dbname={$dbname};port={$port};",
                $config->get('DB_USERNAME'),
                $config->get('DB_PASSWORD'),
                [
                    'options' => [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ],
                ],
            );
        } catch (PDOException $e) {
            $this->logger->info("database name: {$dbname}");
            $this->logger->error('Internal Server Error: 500', ['Error' => $e]);
            die('Error Interno - Consulte al administrador');
        }
    }
}

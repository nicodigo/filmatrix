<?php

namespace App\Core\Traits;

use Monolog\Logger;

trait Loggable
{

    public Logger $logger;

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}

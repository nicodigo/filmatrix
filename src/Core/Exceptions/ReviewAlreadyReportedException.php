<?php

namespace App\Core\Exceptions;

class ReviewAlreadyReportedException extends \RuntimeException
{
    public function __construct(string $message = "Ya reportaste esta reseña")
    {
        parent::__construct($message);
    }
}
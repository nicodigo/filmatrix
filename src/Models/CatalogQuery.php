<?php

namespace App\Models;

class CatalogQuery
{
    public function __construct(
        public readonly ?int    $genreId,
        public readonly ?int    $year,
        public readonly ?string $language,
        public readonly ?float  $minScore,
        public readonly int     $page = 1,
    ) {}
}

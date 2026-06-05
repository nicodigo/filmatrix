<?php

namespace App\Dtos;

class CatalogQuery
{
    public function __construct(
        public readonly ?int    $genreId,
        public readonly ?int    $year,
        public readonly ?string $language,
        public readonly ?float  $minScore,
        public readonly int     $page = 1,
        public readonly string  $sort = 'release_year',
    ) {}
}

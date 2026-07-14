<?php

declare(strict_types=1);

namespace App\Core\Http;

class Links
{
    /**
     * @param array<string, array{href: string, method: string}> $rels
     */
    public static function build(array $rels): array
    {
        $out = [];

        foreach ($rels as $rel => $link) {
            $out[] = [
                'rel'    => $rel,
                'href'   => $link['href'],
                'method' => $link['method'],
            ];
        }

        return $out;
    }
}

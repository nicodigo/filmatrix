<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Core\Http\Links;
use App\Models\WatchlistItem;

class WatchlistResource
{
    public static function fromItem(WatchlistItem $item): array
    {
        return [
            'id'         => $item->id,
            'title_id'   => $item->titleId,
            'status'     => $item->status,
            'added_at'   => $item->addedAt,
            'updated_at' => $item->updatedAt,
            'links'      => Links::build([
                'self'   => ['href' => "/api/v1/watchlist?title_id={$item->titleId}", 'method' => 'GET'],
                'update' => ['href' => '/api/v1/watchlist', 'method' => 'PATCH'],
                'delete' => ['href' => '/api/v1/watchlist', 'method' => 'DELETE'],
                'title'  => ['href' => "/titles/detail?id={$item->titleId}", 'method' => 'GET'],
            ]),
        ];
    }

    public static function collection(array $items): array
    {
        return [
            'data'  => array_map([self::class, 'fromItem'], $items),
            'links' => Links::build([
                'self'   => ['href' => '/api/v1/watchlist', 'method' => 'GET'],
                'create' => ['href' => '/api/v1/watchlist', 'method' => 'POST'],
            ]),
        ];
    }
}

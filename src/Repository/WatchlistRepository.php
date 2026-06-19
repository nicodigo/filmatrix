<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dtos\WatchlistEntry;
use App\Models\WatchlistItem;
use PDO;

class WatchlistRepository
{
    public function __construct(private PDO $db) {}

    /** @return WatchlistItem[] */
    public function findByUser(int $userId, ?string $status = null): array
    {
        $sql = 'SELECT * FROM watchlist_items WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($status !== null) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY updated_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(
            fn(array $row) => WatchlistItem::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findByUserPaginated(int $userId, int $limit, int $offset, ?string $status = null): array
    {
        $sql = '
        SELECT
            w.id, w.user_id, w.title_id, w.status, w.added_at, w.updated_at,
            t.tmdb_id, t.title, t.poster_url, t.release_year, t.type
        FROM watchlist_items w
        JOIN titles t ON t.id = w.title_id
        WHERE w.user_id = :user_id';

        if ($status !== null) {
            $sql .= ' AND w.status = :status';
        }

        $sql .= ' ORDER BY w.updated_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset',  $offset, PDO::PARAM_INT);

        if ($status !== null) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }

        $stmt->execute();

        return array_map(
            fn(array $row) => WatchlistEntry::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findByUserAndTitle(int $userId, int $titleId): ?WatchlistItem
    {
        $sql = 'SELECT * FROM watchlist_items WHERE user_id = :user_id AND title_id = :title_id';
        $params = [
            'user_id' => $userId,
            'title_id' => $titleId,
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? WatchlistItem::fromRow($row) : null;
    }

    public function insert(int $userId, int $titleId, string $status): WatchlistItem
    {
        $sql = 'INSERT INTO watchlist_items (user_id, title_id, status, added_at, updated_at)' .
            ' VALUES (:user_id, :title_id, :status, :added_at, :updated_at)' .
            ' RETURNING *';
        $now = date('Y-m-d H:i:s');
        $params = [
            'user_id' => $userId,
            'title_id' => $titleId,
            'status' => $status,
            'added_at' => $now,
            'updated_at' => $now,
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return WatchlistItem::fromRow($row);
    }

    public function updateStatus(int $userId, int $titleId, string $status): bool
    {
        $sql = 'UPDATE watchlist_items' .
            ' SET status = :status, updated_at = :updated_at' .
            ' WHERE user_id = :user_id AND title_id = :title_id';

        $now = date('Y-m-d H:i:s');
        $params = [
            'user_id' => $userId,
            'title_id' => $titleId,
            'status' => $status,
            'updated_at' => $now,
        ];

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $userId, int $titleId): bool
    {
        $sql = 'DELETE FROM watchlist_items' .
            ' WHERE title_id = :title_id AND user_id = :user_id';

        $params = [
            'user_id' => $userId,
            'title_id' => $titleId,
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function countUserWatchlistItems(int $userId, ?string $status): int
    {
        $sql = 'SELECT COUNT(*) FROM watchlist_items' .
            ' WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($status !== null) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }
}
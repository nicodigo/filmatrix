<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dtos\ListCardDto;
use App\Dtos\ListItemEntry;
use App\Models\UserList;
use PDO;

class UserListRepository
{
    public function __construct(private PDO $db) {}

    /** @return UserList[] */
    public function findByUser(int $userId): array
    {
        $sql = 'SELECT * FROM lists WHERE user_id = :user_id ORDER BY updated_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return array_map(
            fn(array $row) => UserList::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** @return ListCardDto[] */
    public function findByUserWithCounts(int $userId): array
    {
        $sql = 'SELECT
                    l.id,
                    l.name,
                    l.is_public,
                    l.updated_at,
                    COUNT(li.title_id) AS item_count
                FROM lists l
                LEFT JOIN list_items li ON li.list_id = l.id
                WHERE l.user_id = :user_id
                GROUP BY l.id, l.name, l.is_public, l.updated_at
                ORDER BY l.updated_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return array_map(
            fn(array $row) => new ListCardDto(
                id:        (int) $row['id'],
                name:      $row['name'],
                isPublic:  (bool) $row['is_public'],
                itemCount: (int) $row['item_count'],
                updatedAt: $row['updated_at'],
            ),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function findById(int $listId): ?UserList
    {
        $sql = 'SELECT * FROM lists WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $listId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? UserList::fromRow($row) : null;
    }

    public function insert(int $userId, string $name, bool $isPublic): UserList
    {
        $sql = 'INSERT INTO lists (user_id, name, is_public, created_at, updated_at)
                VALUES (:user_id, :name, :is_public, :now, :now)
                RETURNING *';
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'   => $userId,
            'name'      => $name,
            'is_public' => $isPublic ? 't' : 'f',
            'now'       => $now,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return UserList::fromRow($row);
    }

    public function update(int $listId, string $name, bool $isPublic): bool
    {
        $sql = 'UPDATE lists
                SET name = :name, is_public = :is_public, updated_at = :updated_at
                WHERE id = :id';
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id'         => $listId,
            'name'       => $name,
            'is_public'  => $isPublic ? 't' : 'f',
            'updated_at' => $now,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $listId): bool
    {
        $sql = 'DELETE FROM lists WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $listId]);

        return $stmt->rowCount() > 0;
    }

    /** @return ListItemEntry[] */
    public function findItemsByList(int $listId, int $limit, int $offset): array
    {
        $sql = 'SELECT
                    li.list_id,
                    li.title_id,
                    li.added_at,
                    t.tmdb_id,
                    t.title,
                    t.poster_url,
                    t.release_year
                FROM list_items li
                JOIN titles t ON t.id = li.title_id
                WHERE li.list_id = :list_id
                ORDER BY li.added_at DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':list_id', $listId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset',  $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            fn(array $row) => ListItemEntry::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function countItems(int $listId): int
    {
        $sql = 'SELECT COUNT(*) FROM list_items WHERE list_id = :list_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['list_id' => $listId]);

        return (int) $stmt->fetchColumn();
    }

    public function addItem(int $listId, int $titleId): void
    {
        $now = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO list_items (list_id, title_id, added_at)
                VALUES (:list_id, :title_id, :added_at)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'list_id'  => $listId,
            'title_id' => $titleId,
            'added_at' => $now,
        ]);
    }

    public function removeItem(int $listId, int $titleId): bool
    {
        $sql = 'DELETE FROM list_items WHERE list_id = :list_id AND title_id = :title_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'list_id'  => $listId,
            'title_id' => $titleId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function itemExists(int $listId, int $titleId): bool
    {
        $sql = 'SELECT 1 FROM list_items WHERE list_id = :list_id AND title_id = :title_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'list_id'  => $listId,
            'title_id' => $titleId,
        ]);

        return $stmt->fetchColumn() !== false;
    }

    public function countByUser(int $userId): int
    {
        $sql = 'SELECT COUNT(*) FROM lists WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }
}

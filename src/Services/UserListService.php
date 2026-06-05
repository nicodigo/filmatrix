<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Exceptions\ListItemAlreadyExistsException;
use App\Core\Exceptions\ListNotFoundException;
use App\Core\Exceptions\UnauthorizedListAccessException;
use App\Dtos\ListCardDto;
use App\Dtos\ListDetailResult;
use App\Models\UserList;
use App\Repository\UserListRepository;
use InvalidArgumentException;

class UserListService
{
    private const int MAX_LISTS_PER_USER = 50;
    private const int MAX_LIST_NAME_LENGTH = 150;
    private const int ITEMS_PER_PAGE = 40;

    public function __construct(
        private UserListRepository $userListRepository,
    ) {}

    /** @return ListCardDto[] */
    public function getUserLists(int $userId): array
    {
        return $this->userListRepository->findByUserWithCounts($userId);
    }

    public function getListDetail(int $listId, int $currentUserId, int $page = 1): ListDetailResult
    {
        $list = $this->userListRepository->findById($listId);

        if ($list === null) {
            throw new ListNotFoundException("List $listId not found");
        }

        $isOwner = $list->userId === $currentUserId;

        if (!$list->isPublic && !$isOwner) {
            throw new UnauthorizedListAccessException("This list is private");
        }

        $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        $items = $this->userListRepository->findItemsByList($listId, self::ITEMS_PER_PAGE, $offset);
        $total = $this->userListRepository->countItems($listId);
        $totalPages = max(1, (int) ceil($total / self::ITEMS_PER_PAGE));

        return new ListDetailResult(
            items:       $items,
            currentPage: $page,
            totalPages:  $totalPages,
            listName:    $list->name,
            listId:      $list->id,
            isPublic:    $list->isPublic,
            isOwner:     $isOwner,
        );
    }

    public function createList(int $userId, string $name, bool $isPublic): UserList
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('List name cannot be empty');
        }

        if (mb_strlen($name) > self::MAX_LIST_NAME_LENGTH) {
            throw new InvalidArgumentException('List name is too long');
        }

        $count = $this->userListRepository->countByUser($userId);
        if ($count >= self::MAX_LISTS_PER_USER) {
            throw new InvalidArgumentException('Maximum number of lists reached');
        }

        return $this->userListRepository->insert($userId, $name, $isPublic);
    }

    public function updateList(int $listId, int $userId, string $name, bool $isPublic): UserList
    {
        $list = $this->userListRepository->findById($listId);

        if ($list === null) {
            throw new ListNotFoundException("List $listId not found");
        }

        if ($list->userId !== $userId) {
            throw new UnauthorizedListAccessException("You cannot edit this list");
        }

        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('List name cannot be empty');
        }

        if (mb_strlen($name) > self::MAX_LIST_NAME_LENGTH) {
            throw new InvalidArgumentException('List name is too long');
        }

        $this->userListRepository->update($listId, $name, $isPublic);

        return $this->userListRepository->findById($listId);
    }

    public function deleteList(int $listId, int $userId): bool
    {
        $list = $this->userListRepository->findById($listId);

        if ($list === null) {
            throw new ListNotFoundException("List $listId not found");
        }

        if ($list->userId !== $userId) {
            throw new UnauthorizedListAccessException("You cannot delete this list");
        }

        return $this->userListRepository->delete($listId);
    }

    public function addTitleToList(int $listId, int $userId, int $titleId): void
    {
        $list = $this->userListRepository->findById($listId);

        if ($list === null) {
            throw new ListNotFoundException("List $listId not found");
        }

        if ($list->userId !== $userId) {
            throw new UnauthorizedListAccessException("You cannot modify this list");
        }

        if ($this->userListRepository->itemExists($listId, $titleId)) {
            throw new ListItemAlreadyExistsException("Title $titleId already exists in list $listId");
        }

        $this->userListRepository->addItem($listId, $titleId);
    }

    public function removeTitleFromList(int $listId, int $userId, int $titleId): bool
    {
        $list = $this->userListRepository->findById($listId);

        if ($list === null) {
            throw new ListNotFoundException("List $listId not found");
        }

        if ($list->userId !== $userId) {
            throw new UnauthorizedListAccessException("You cannot modify this list");
        }

        return $this->userListRepository->removeItem($listId, $titleId);
    }

    /** @return ListCardDto[] */
    public function getAvailableListsForTitle(int $userId, int $titleId): array
    {
        $lists = $this->userListRepository->findByUserWithCounts($userId);

        return array_map(
            fn(ListCardDto $dto) => new ListCardDto(
                id:        $dto->id,
                name:      $dto->name,
                isPublic:  $dto->isPublic,
                itemCount: $dto->itemCount,
                updatedAt: $dto->updatedAt,
            ),
            $lists
        );
    }
}

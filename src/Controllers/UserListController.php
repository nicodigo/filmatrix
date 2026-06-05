<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Exceptions\ListNotFoundException;
use App\Core\Exceptions\ListItemAlreadyExistsException;
use App\Core\Exceptions\UnauthorizedListAccessException;
use App\Core\Request;
use App\Services\UserListService;
use Exception;
use RuntimeException;
use Twig\Environment;

class UserListController
{
    private Environment $twig;
    private UserListService $userListService;
    private Request $request;

    public function __construct(UserListService $userListService, Environment $twig, Request $request)
    {
        $this->userListService = $userListService;
        $this->twig = $twig;
        $this->request = $request;
    }

    // GET /my-lists
    public function index(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists index");
        }

        $lists = $this->userListService->getUserLists($userId);

        $this->twig->display('pages/my-lists.html.twig', [
            'lists' => $lists,
        ]);
    }

    // GET /my-lists/detail?id=...
    public function show(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists detail");
        }

        $listId = (int) $this->request->get('id', 0);
        $page   = max(1, (int) $this->request->get('page', 1));

        try {
            $result = $this->userListService->getListDetail($listId, $userId, $page);
        } catch (ListNotFoundException) {
            http_response_code(404);
            echo $this->twig->render('pages/error-404.html.twig');
            return;
        } catch (UnauthorizedListAccessException) {
            http_response_code(403);
            echo $this->twig->render('pages/error-404.html.twig');
            return;
        }

        $prevUrl = null;
        $nextUrl = null;

        if ($result->hasPrevPage()) {
            $prevUrl = '/my-lists/detail?id=' . $listId . '&page=' . ($result->currentPage - 1);
        }

        if ($result->hasNextPage()) {
            $nextUrl = '/my-lists/detail?id=' . $listId . '&page=' . ($result->currentPage + 1);
        }

        $this->twig->display('pages/list-detail.html.twig', [
            'list'     => $result,
            'listId'   => $listId,
            'prevUrl'  => $prevUrl,
            'nextUrl'  => $nextUrl,
        ]);
    }

    // POST /my-lists
    public function store(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists post");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $name     = trim((string) ($body['name'] ?? ''));
        $isPublic = (bool) ($body['is_public'] ?? false);
        header('Content-Type: application/json');

        try {
            $list = $this->userListService->createList($userId, $name, $isPublic);
            echo json_encode([
                'success'   => true,
                'list'      => [
                    'id'        => $list->id,
                    'name'      => $list->name,
                    'is_public' => $list->isPublic,
                ],
            ]);
        } catch (Exception $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // PATCH /my-lists
    public function update(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists patch");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $listId   = (int) ($body['list_id'] ?? 0);
        $name     = trim((string) ($body['name'] ?? ''));
        $isPublic = (bool) ($body['is_public'] ?? false);
        header('Content-Type: application/json');

        try {
            $list = $this->userListService->updateList($listId, $userId, $name, $isPublic);
            echo json_encode([
                'success' => true,
                'list'    => [
                    'id'        => $list->id,
                    'name'      => $list->name,
                    'is_public' => $list->isPublic,
                ],
            ]);
        } catch (Exception $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // DELETE /my-lists
    public function delete(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists delete");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $listId = (int) ($body['list_id'] ?? 0);
        header('Content-Type: application/json');

        try {
            $this->userListService->deleteList($listId, $userId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // POST /my-lists/item
    public function addItem(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists addItem");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $listId  = (int) ($body['list_id'] ?? 0);
        $titleId = (int) ($body['title_id'] ?? 0);
        header('Content-Type: application/json');

        try {
            $this->userListService->addTitleToList($listId, $userId, $titleId);
            echo json_encode(['success' => true]);
        } catch (ListItemAlreadyExistsException $e) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // DELETE /my-lists/item
    public function removeItem(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists removeItem");
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $listId  = (int) ($body['list_id'] ?? 0);
        $titleId = (int) ($body['title_id'] ?? 0);
        header('Content-Type: application/json');

        try {
            $this->userListService->removeTitleFromList($listId, $userId, $titleId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // GET /my-lists/available?title_id=...
    public function available(): void
    {
        $userId = $this->request->session('user_id');
        if ($userId === null) {
            throw new RuntimeException("User id null on my-lists available");
        }

        $titleId = (int) $this->request->get('title_id', 0);
        $lists   = $this->userListService->getUserLists($userId);
        header('Content-Type: application/json');

        $mapped = array_map(
            fn($list) => [
                'id'         => $list->id,
                'name'       => $list->name,
                'is_public'  => $list->isPublic,
                'item_count' => $list->itemCount,
            ],
            $lists
        );

        echo json_encode(['success' => true, 'lists' => $mapped]);
    }
}

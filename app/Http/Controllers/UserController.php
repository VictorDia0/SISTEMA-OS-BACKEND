<?php

namespace App\Http\Controllers;

use App\Collections\UserCollection;
use App\Services\IUserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(protected IUserService $userService) {}

    public function buscarTodosUsuarios(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return ResponseService::success(UserCollection::make($users), code: Response::HTTP_OK);
    }

    public function buscarUsuarioPorId(string $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        return ResponseService::success(UserCollection::make($user), code: Response::HTTP_OK);
    }
}

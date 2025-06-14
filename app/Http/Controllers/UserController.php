<?php

namespace App\Http\Controllers;

use App\Collections\UserCollection;
use App\Http\Requests\FilterOrderRequest;
use App\Http\Requests\UserRequest;
use App\Jobs\JobSendWelcomeEmail;
use App\Models\User;
use App\Services\IUserService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(protected IUserService $userService) {}

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return ResponseService::success(UserCollection::make($users), code: Response::HTTP_OK);
    }

    public function getAllOrdersByUser(FilterOrderRequest $request, ?User $user = null): JsonResponse
    {
        $data = (object) $request->validated();

        if (is_null($user)) {
            $user = Auth::user();
        }

        $orders = $this->userService->getAllOrdersByUser($user, $data);

        return ResponseService::success($orders, 'feito', code: Response::HTTP_OK);
    }

    private function findUserById(string $id): User
    {
        return User::findOrFail($id);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->findUserById($id);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'User retrieved successfully',
                    'data' => $user,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User not found',
                    'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                ],
                404
            );
        }
    }

    /**
     * Cria novo usuário com os dados fornecidos na requisição.
     *
     * @param  \App\Http\Requests\UserRequest  $request O objeto de requisição contendo os dados do usuário a ser criado.
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        // Iniciar a transação
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'plan' => 'basic',
                'account_status' => 'active',
                'payment_status' => 'paid',
            ]);

            DB::commit();

            JobSendWelcomeEmail::dispatch($user->id)->onQueue('default');

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Usuário criado com sucesso',
                    'user' => $user,
                ],
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to create user',
                    'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                ],
                500
            );
        }
    }

    public function update(UserRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $this->findUserById($id);

            $user->fill($request->except(['password']));

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'User updated successfully',
                    'data' => $user,
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to update user',
                    'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                ],
                500
            );
        }
    }

    public function destroy(string $id): JsonResponse
    {
        $user = $this->findUserById($id);
        try {
            $user->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'User deleted successfully',
                    'data' => $user,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to delete user',
                    'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                ],
                400
            );
        }
    }
}

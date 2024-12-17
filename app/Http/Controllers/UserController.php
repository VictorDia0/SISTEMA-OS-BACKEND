<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Jobs\JobSendWelcomeEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Retorna uma lista paginada de usuários.
     *
     * Este método recupera uma lista paginada de usuários do banco de dados
     * e a retorna como uma resposta JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = User::paginate(10); // Paginação com 10 usuários por página
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ], 200);
    }


    /**
     * Exibe os detalhes de um usuário específico.
     *
     * Este método retorna os detalhes de um usuário específico em formato JSON.
     *
     * @param  \App\Models\User  $user O objeto do usuário a ser exibido
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'User retrieved successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 404);
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
            //Cadastrar usuario
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'plan' => 'basic', // Valor padrão
                'account_status' => 'active', // Valor padrão
                'payment_status' => 'paid', // Valor padrão
            ]);

            // Cometer a transação
            DB::commit();

            //Agendar envio de email
            JobSendWelcomeEmail::dispatch($user->id)->onQueue('default');

            // Retorna a resposta de sucesso
            return response()->json([
                'status' => true,
                'message' => 'Usuário criado com sucesso',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {

            // Reverter a transação em caso de erro
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create user',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }


    /**
     * Atualizar os dados de um usuário existente com base nos dados fornecidos na requisição.
     *
     * @param  \App\Http\Requests\UserRequest  $request O objeto de requisição contendo os dados do usuário a ser atualizado.
     * @param  \App\Models\User  $user O usuário a ser atualizado.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            //Buscando o usuario pelo ID
            $user = User::findOrFail($id);

            // Atualizar a senha somente se ela for fornecida na requisição.
            $user->fill($request->except(['password']));

            // Atualiza a senha apenas se fornecida.
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {

            // Reverter a transação em caso de erro
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to update user',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Excluir usuário no banco de dados.
     *
     * @param  \App\Models\User  $user O usuário a ser excluído.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        try {
            $user->delete();

            // Retorna os dados do usuário apagado e uma mensagem de sucesso com status 200
            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete user',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 400);
        }
    }
}

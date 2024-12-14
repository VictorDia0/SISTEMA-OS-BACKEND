<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Jobs\JobSendWelcomeEmail;
use App\Mail\ValidationEmail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::paginate();
        return response()->json($users, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::find($id);
            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['message' => 'User not found' . $e], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request) : JsonResponse
    {
        // Iniciar a transação
        DB::beginTransaction();

        try {
            //Cadastrar usuario
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'phone_number' => $request->celular,
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
                'message' => 'Falha ao cadastrar o usuário',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:20',
            'surname' => 'sometimes|string|max:100',
            'celular' => 'sometimes|string|max:20',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $user->fill($request->all());

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update user'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::paginate(10); // Define o número de resultados por página
        return response()->json($employees);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $employee = Employee::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Employee retrieved successfully',
                'data' => $employee,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request, User $user)
    {
        // Validar se o limite do plano foi atingido
        $employeeCount = $user->employees()->count();
        $planLimits = [
            'basic' => 10,
            'medium' => 25,
            'pro' => 50,
        ];

        if ($employeeCount >= $planLimits[$user->plan]) {
            return response()->json([
                'status' => false,
                'message' => 'Você atingiu o limite de funcionários para o seu plano.',
            ], 403);
        }

        // Adicionar o ID do usuário e criptografar a senha
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
        $validatedData['user_id'] = $user->id;

        try {
            $employee = Employee::create($validatedData);
            return response()->json(['message' => 'Funcionário criado com sucesso', 'employee' => $employee], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Falha ao criar funcionário', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, string $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validatedData = $request->validated();

            // Atualizar senha se fornecida
            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            }

            $employee->update($validatedData);

            return response()->json(['message' => 'Funcionário atualizado com sucesso', 'employee' => $employee], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao atualizar funcionário',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $employee->delete();

            return response()->json(['message' => 'Funcionário excluído com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao excluir funcionário',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

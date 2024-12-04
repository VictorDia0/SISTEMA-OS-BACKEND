<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employee = Employee::all();
        return response()->json($employee);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'surname' => 'required|string|max:100',
            'RG' => 'nullable|string|max:15',
            'CPF' => 'required|string|size:11|unique:employees,CPF',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:8',
            'CEP' => 'required|string|size:8',
            'rua' => 'required|string|max:100',
            'numero' => 'required|string|max:10',
            'bairro' => 'required|string|max:50',
            'estado' => 'required|string|size:2',
            'situacao' => 'nullable|in:Ativo,Inativo',
            'permissao' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $employee = Employee::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'RG' => $request->RG,
                'CPF' => $request->CPF,
                'telefone' => $request->telefone,
                'celular' => $request->celular,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'CEP' => $request->CEP,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'estado' => $request->estado,
                'situacao' => $request->situacao ?? 'Ativo',
                'permissao' => $request->permissao ?? 'tecnico',
            ]);

            return response()->json(['message' => 'Employee created successfully', 'employee' => $employee], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create employee', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee =  Employee::fing($id);
        if (!$employee) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

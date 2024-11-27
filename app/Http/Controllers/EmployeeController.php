<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = Employees::all();
        return response()->json($users);
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
            'Name' => 'required|string|max:20',
            'Surname' => 'required|string|max:100',
            'RG' => 'nullable|string|max:15',
            'CPF' => 'required|string|size:11|unique:users,CPF',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
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
            $user = Employees::create([
                'Name' => $request->Name,
                'Surname' => $request->Surname,
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
                'permissao' => $request->permissao,
            ]);
            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user =  Employees::fing($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);

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

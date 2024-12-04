<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($credentials)){

            $user = Auth::user();
            $token = $request->user()->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'email' => $user->email,
                'name' => $user->name,
                // 'url' => 'http://127.0.0.1:8000/api/users',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Login ou Senha incorretos'
            ], 404);
        }
    }
}

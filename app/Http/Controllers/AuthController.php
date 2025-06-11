<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\LoginSession;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Handle user login and return a token if successful.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $request->user()->createToken('api-token')->plainTextToken;

                $device = $request->header('User-Agent') ?? ' Unknown Device';
                $ipAddress = $request->ip();

                // Registrar a sessão no banco de dados
                LoginSession::create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'device' => $device,
                    'ip_address' => $ipAddress,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                    'token_type' => 'bearer',
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(User $id): JsonResponse
    {
        try {
            $id->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to log out.'
            ], 400);
        }
    }

    /**
     * Log the user out from all devices (Invalidate all tokens).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutFromAllDevices(User $user): JsonResponse
    {
        try {
            // Verifica se o usuário tem tokens antes de tentar excluir
            if ($user->tokens->count() > 0) {
                $user->tokens()->delete();
            }

            // Verifica se o usuário tem sessões antes de tentar excluir
            if (DB::table('login_sessions')->where('user_id', $user->id)->exists()) {
                DB::table('login_sessions')->where('user_id', $user->id)->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out from all devices.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to log out from all devices.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    public function sessions(): JsonResponse
    {
        $user = Auth::user();

        $sessions = LoginSession::where('user_id', $user->id)->get();

        return response()->json([
            'status' => true,
            'sessions' => $sessions
        ], 200);
    }
}

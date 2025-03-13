<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Broadcast;
use App\Repositories\User\UserRepositoryInterface;

class AuthController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function users()
    {
        return response()->json($this->userRepository->all());
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'user' => $user,
            'token' => JWTAuth::fromUser($user)
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'user' => Auth::user(),
            'token' => $token
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'token' => Auth::refresh()
        ]);
    }

    public function broadcastingLogin(Request $request)
    {
        try {
            // Pega o token do cabeçalho da requisição
            $token = $request->header('Authorization');
    
            // Remove "Bearer " para pegar apenas o token puro
            $token = str_replace('Bearer ', '', $token);
    
            // Autentica o usuário pelo token JWT
            $user = JWTAuth::parseToken()->authenticate();
    
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            return Broadcast::auth($request);
        } catch (Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}

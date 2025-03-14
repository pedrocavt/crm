<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\LoginRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Broadcast;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function users(): JsonResponse
    {
        return response()->json($this->userRepository->all());
    }

    public function register(CreateUserRequest $request): JsonResponse
    {
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

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'user' => Auth::user(),
            'token' => $token
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function refresh(): JsonResponse
    {
        return response()->json([
            'user' => Auth::user(),
            'token' => Auth::refresh()
        ]);
    }

    public function broadcastingLogin(Request $request): mixed
    {
        try {
            $token = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $token);
    
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

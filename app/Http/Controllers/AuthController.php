<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {}

    public function register(RegisterRequest $request)
    {

        $userData = $this->authService->register($request->validated());

        return response()->json($userData, 201);

    }

    public function login(LoginRequest $request)
    {

        $result = $this->authService->login($request->validated());

        if (! $result) {
            return response()->json([
                'message' => 'Неверные данные',
            ], 422);

        }

        return response()->json($result);

    }

    public function logout(Request $request)
    {

        $this->authService->logout($request->user);

        return response()->json([
            'message' => 'You logout bro!!!',
        ]);
    }

    public function me(Request $request)
    {
        return $request->user;
    }
}

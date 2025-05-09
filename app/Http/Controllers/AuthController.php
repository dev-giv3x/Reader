<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\RegisterRequest;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
   
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        $token = User::where('email', $request->email)->first();
         
        $token->remember_token = Str::random(60);
        $token->save();

      
        return response()->json([
            'access_token' => $token->remember_token,
            'user' => $token,
        ], 201);

    }


    public function book(Request $request){
        dd($request->user);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Неверные данные'], 422);
        }

       $token = User::where('email', $request->email)->first();

       $token->remember_token = Str::random(60);
       $token->save();

        return response()->json([
            'access_token' => $token->remember_token,
            'user' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user->remember_token = NULL;
        $request->user->save();
        
        return response()->json([
            'message' => 'You logout bro!!!',
        ]);
    }

    public function me(Request $request)
    {
        return$request->user;
    }
}
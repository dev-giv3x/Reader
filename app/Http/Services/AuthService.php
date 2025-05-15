<?php
namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{

    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => isset($data['is_admin']) ? (bool) $data['is_admin'] : false,
        ]);

        $tokenUser = User::where('email', $data['email'])->first();
        $tokenUser->remember_token = Str::random(60);
        $tokenUser->save();

        return [
            'access_token' => $tokenUser->remember_token,
            'user' => $tokenUser,
        ];
    }

    public function login(array $data)
    {

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return null;
        }

        $user->remember_token = Str::random(60);
        $user->save();

        return [
            'access_token' => $user->remember_token,
            'user'         => $user,
        ];
    }

    public function logout(User $user)
    {
        $user->remember_token = null;
        $user->save();
    }

}

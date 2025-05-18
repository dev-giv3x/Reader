<?php
namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class OptionalAuth
{
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $token = $request->bearerToken();

    //     if ($token) {
    //         $user = User::where('remember_token', $token)->first();
    //         if ($user) {
    //             $request->user = $user;
    //         }
    //     }

    //     return $next($request);
    // }

public function handle(Request $request, Closure $next): Response
{
    $token = $request->bearerToken();
    Log::info("OptionalAuth token: " . ($token ?? 'null'));

    if ($token) {
        $user = User::where('remember_token', $token)->first();
        Log::info("OptionalAuth user found: " . ($user ? $user->id : 'none'));
        if ($user) {
            $request->user = $user;
        }
    } else {
        Log::info("OptionalAuth no token provided");
    }

    return $next($request);
}
}

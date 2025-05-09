<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class OptionalAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token) {
            $user = User::where('remember_token', $token)->first();
            if ($user) {
                $request->user = $user;
            }
        }

        return $next($request);
    }
}

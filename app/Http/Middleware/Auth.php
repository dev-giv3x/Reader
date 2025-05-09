<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = User::where('remember_token', $request->bearerToken())->first();

    //  dd($user);

        throw_if(!$user, new ApiException(403, 'Forbidden for you!!'));

        $request->user = $user;

        // dd($request->user);

        return $next($request);
    }
}

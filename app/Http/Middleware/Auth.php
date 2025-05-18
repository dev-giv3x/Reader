<?php
namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            throw new ApiException(403, 'Forbidden: token missing');
        }

        $user = User::where('remember_token', $request->bearerToken())->first();

        // dd($user);

        throw_if(! $user, new ApiException(403, 'Forbidden for you!!'));

        $request->user = $user;

        // dd($request->user);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class UserAuthenticate
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'error' => 'Token not provided.'
            ], 401);
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch (ExpiredException $e) {
            //function to delete the expired user row
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
        // Now let's put the user in the request class so that you can grab it from there
        return $next($request);
    }
}

<?php
// app/Http/Middleware/EnforceTokenExpiration.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        // Only check if user is authenticated via API
        if ($request->user('api')) {
            $token = $request->user('api')->token();

            if ($token && $token->expires_at->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token expired. Please login again',
                ], 401);
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware\Api\V1\User;

use App\Models\ApiClient;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $api_key = $request->header('api-key');
        $secret_key = $request->header('secret-key');

        if (!$api_key || !$secret_key) {
            return response()->json(['error' => 'API key and secret key are required'], 401);
        }

        $validate_keys = ApiClient::where('api_key', $api_key)
            ->where('secret_key', $secret_key)
            ->first();

        if (!$validate_keys) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Auth::loginUsingId($validate_keys->user_id);

        return $next($request);
    }
}

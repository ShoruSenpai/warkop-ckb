<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtendTokenActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $token = $request->user()?->currentAccessToken();

        if($token && ! $token instanceof \Laravel\Sanctum\TransientToken && $token->exists) {
            $token->forceFill([
                'last_used_at' => now(),
            ])->save();
        }

        return $response;
    }
}

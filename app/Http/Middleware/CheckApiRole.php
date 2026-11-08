<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!in_array($request->user()?->role, $roles, true)) {
            return response()->json([
                "success" => false,
                "message" => "Anda tidak memiliki izin untuk mengakses resource ini.",
            ], 403);
        }

        return $next($request);
    }
}

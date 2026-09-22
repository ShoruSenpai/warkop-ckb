<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckWebRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (in_array($user->role, ['employee', 'cashier'])) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akses ditolak. Web Panel khusus untuk Owner dan Admin.'
            ]);
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki izin mengakses halaman ini.');
        }

        return $next($request);
    }
}

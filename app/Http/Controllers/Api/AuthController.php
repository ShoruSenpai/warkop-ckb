<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// models
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|in:web,mobile',
        ]);

        $user = User::with('employee')->where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        if($user->employee && !$user->employee->is_active)
        {
            return response()->json([
                'success' => false,
                'message' => 'Account is not activated.',
            ], 403);
        }

        $abilities = match($user->role)
        {
          'owner' => ['*'],
          'admin' => ['manage:products', 'manage:purchases', 'manage:orders', 'manage:attendance', ],
          'employee' => ['attendance:submit', 'attendance:read'],
          'cashier' => ['pos:submit', 'pos:read'],
              default => []
        };

        $user->tokens()->where('name', $request->device_name)->delete();

        $token = $user->createToken($request->device_name, $abilities)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login success.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'employee_info' => $user->employee
                ]
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        
        if ($token && ! $token instanceof \Laravel\Sanctum\TransientToken) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout success.',
        ], 200);
    }

}

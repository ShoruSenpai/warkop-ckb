<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    /**
     * Get Settings & Profile Data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('employee');

        // Store profile defaults
        $settings = [
            'store_name' => config('app.name', 'Warkop Cak Kebo'),
            'store_address' => 'Jl. Raya Surabaya No. 123',
            'store_phone' => '081234567890',
            'shift_morning' => '06:00 - 14:00',
            'shift_evening' => '14:00 - 22:00',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'full_name' => $user->employee->full_name ?? $user->username,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $settings,
        ]);
    }

    /**
     * Update user profile settings.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'full_name' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
        ]);

        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($user->employee) {
            $user->employee->update([
                'full_name' => $request->full_name ?? $user->employee->full_name,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
        ]);
    }

    /**
     * Get user management list (Owner/Admin).
     */
    public function users(): JsonResponse
    {
        $users = User::with('employee')->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }
}

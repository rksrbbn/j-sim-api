<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        if (empty($request->username) || empty($request->password)) {
            return response()->json([
                'code' => 400,
                'message' => 'Pastikan Username dan password harus diisi.',
                'data' => null
            ], 400);
        }

        // JIKA IP SUDAH TERDAFTAR
        if (User::where('ip', $request->ip())->exists()) {
            return response()->json([
                'code' => 400,
                'message' => 'IP ini sudah memiliki akun yang terdaftar.',
                'data' => null
            ], 400);
        }

        $existingUser = User::where('username', $request->username)->first();

        if ($existingUser) {
            return response()->json([
                'code' => 400,
                'message' => 'Username sudah digunakan.',
                'data' => null
            ], 400);
        }

        $user = User::create([
            'id' => Str::uuid(),
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'money' => 100,
            'fame' => 0,
            'type' => 'GEN',
            'tier' => 'bronze',
            'days' => 1,
            'is_dev' => 0,
            'ip' => $request->ip()
        ]);

        if (!$user)
        {
            return response()->json([
                'code' => 422,
                'message' => 'Gagal registrasi user.',
                'data' => null
            ], 422);
        }

        return response()->json(['code' => 200,'message' => 'Berhasil Registrasi', 'data' => $user]);
    }

    public function login(Request $request)
    {

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' => 401,
                'message' => 'Username atau password salah.',
                'data' => null
            ], 401);
        }

        $existingUserTokens = PersonalAccessToken::where('tokenable_id', $user->id)->count();

        if ($existingUserTokens > 0) {
            $delete = PersonalAccessToken::where('tokenable_id', $user->id)->delete();

            if (!$delete) {
                return response()->json([
                    'code' => 422,
                    'message' => 'Gagal refresh token.',
                    'data' => null
                ], 422);
            }
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['code' => 200,'message' => 'Login berhasil', 'data' => ['username' => $user->username,'userid' => $user->id, 'token' => $token] ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function checkToken (Request $request)
    {
        try {
            $user = $request->user();
            $userId = $request->user_id;
            if ($user && $user->id == $userId) {
                return response()->json(['message' => 'Token valid', 'isValid' => true]);
            }
            
            return response()->json(['message' => 'Invalid token', 'isValid' => false], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error validating token', 'isValid' => false], 500);
        }

    }
}

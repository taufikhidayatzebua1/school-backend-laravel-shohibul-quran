<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user
     * HANYA bisa dilakukan oleh tata-usaha, admin, super-admin
     * Endpoint ini untuk registrasi oleh admin, bukan self-registration
     */
    public function register(StoreUserRequest $request)
    {
        // Cek apakah ada user yang login (authentication check)
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Only tata-usaha, admin, or super-admin can register new users.',
            ], 401);
        }

        // Cek apakah user memiliki role yang diizinkan (authorization check)
        if (!$request->user()->hasAnyRole(['tata-usaha', 'admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only tata-usaha, admin, or super-admin can register new users.',
            ], 403);
        }

        // Validasi role sudah dilakukan di StoreUserRequest
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // role wajib diisi sekarang
        ];

        // Optional fields
        if ($request->filled('username')) {
            $userData['username'] = $request->username;
        }

        if ($request->has('is_active')) {
            $userData['is_active'] = $request->is_active;
        }

        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user' => new UserResource($user),
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::channel('security')->info('Failed login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'request_id' => app('request-id'),
                'time' => now()->toIso8601String()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $expiresIn = config('sanctum.expiration') ? config('sanctum.expiration') * 60 : null; // seconds
        $expiresAt = $expiresIn ? now()->addSeconds($expiresIn)->toIso8601String() : null;

        Log::channel('security')->info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'request_id' => app('request-id'),
            'time' => now()->toIso8601String()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Get authenticated user profile with role-specific data
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        // Eager load role-specific relations
        switch ($user->role) {
            case 'siswa':
                $user->load('siswa.kelas');
                break;
            case 'orang-tua':
                $user->load('orangTua');
                break;
            case 'guru':
            case 'wali-kelas':
            case 'kepala-sekolah':
                $user->load('guru');
                break;
            default:
                // tata-usaha, yayasan, admin, super-admin - no additional relations
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => new UserProfileResource($user)
        ], 200);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password',
            'password' => 'sometimes|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update name and email
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        // Update password if provided
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Reload with role-specific relations
        switch ($user->role) {
            case 'siswa':
                $user->load('siswa.kelas');
                break;
            case 'orang-tua':
                $user->load('orangTua');
                break;
            case 'guru':
            case 'wali-kelas':
            case 'kepala-sekolah':
                $user->load('guru');
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserProfileResource($user)
        ], 200);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send reset link'
        ], 500);
    }

    /**
     * Reset password
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __($status)
        ], 500);
    }

    /**
     * Revoke all tokens
     */
    public function revokeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All tokens revoked successfully'
        ], 200);
    }
}

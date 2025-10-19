<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     * Hanya tata-usaha, admin, dan super-admin yang bisa akses
     */
    public function index(Request $request)
    {
        // Authorization check
        Gate::authorize('viewAny', User::class);

        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $role = $request->input('role');
        $isActive = $request->input('is_active');

        $query = User::query();

        // Filter by search (name, email, username)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role) {
            $query->where('role', $role);
        }

        // Filter by is_active
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'from' => $users->firstItem(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
            ]
        ], 200);
    }

    /**
     * Store a newly created user.
     * Hanya tata-usaha, admin, dan super-admin yang bisa create user
     */
    public function store(StoreUserRequest $request)
    {
        // Authorization check
        Gate::authorize('create', User::class);

        // Data sudah divalidasi oleh StoreUserRequest termasuk role permission
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
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
            'message' => 'User created successfully',
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        // Authorization check
        Gate::authorize('view', $user);

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);

        // Authorization check
        Gate::authorize('update', $user);

        // Data sudah divalidasi oleh UpdateUserRequest termasuk role permission
        $updateData = [];

        if ($request->filled('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->filled('username')) {
            $updateData['username'] = $request->username;
        }

        if ($request->filled('email')) {
            $updateData['email'] = $request->email;
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Role dan is_active sudah divalidasi di UpdateUserRequest
        if ($request->has('role')) {
            $updateData['role'] = $request->role;
        }

        if ($request->has('is_active')) {
            $updateData['is_active'] = $request->is_active;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user->fresh()),
        ], 200);
    }

    /**
     * Remove the specified user.
     * Hanya admin dan super-admin yang bisa delete user
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Authorization check
        Gate::authorize('delete', $user);

        // Soft delete atau hard delete bisa disesuaikan
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }

    /**
     * Get available roles for authenticated user to create/assign
     */
    public function availableRoles(Request $request)
    {
        $user = $request->user();

        $allRoles = [
            'siswa' => 'Siswa',
            'orang-tua' => 'Orang Tua',
            'guru' => 'Guru',
            'wali-kelas' => 'Wali Kelas',
            'kepala-sekolah' => 'Kepala Sekolah',
            'tata-usaha' => 'Tata Usaha',
            'yayasan' => 'Yayasan',
            'admin' => 'Admin',
            'super-admin' => 'Super Admin',
        ];

        $availableRoles = [];

        if ($user->isTataUsaha()) {
            $allowedRoles = ['siswa', 'orang-tua', 'guru', 'wali-kelas'];
            foreach ($allowedRoles as $role) {
                $availableRoles[$role] = $allRoles[$role];
            }
        } elseif ($user->isAdmin()) {
            foreach ($allRoles as $role => $label) {
                if (!in_array($role, ['admin', 'super-admin'])) {
                    $availableRoles[$role] = $label;
                }
            }
        } elseif ($user->isSuperAdmin()) {
            foreach ($allRoles as $role => $label) {
                if ($role !== 'super-admin') {
                    $availableRoles[$role] = $label;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Available roles retrieved successfully',
            'data' => [
                'roles' => $availableRoles,
                'user_role' => $user->role,
            ]
        ], 200);
    }
}

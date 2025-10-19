<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Http\Resources\GuruResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Validate request parameters
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'role' => 'sometimes|in:guru,wali-kelas,kepala-sekolah',
            'sort_by' => 'sometimes|in:nama,nip,tempat_lahir,tanggal_lahir',
            'sort_order' => 'sometimes|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Guru::query();

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by role (guru, wali-kelas, kepala-sekolah)
        if ($request->has('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->input('role'));
            });
        }

        // Search by nama, nip, or no_hp
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'nama');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 15);
        $guru = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diambil',
            'data' => GuruResource::collection($guru),
            'meta' => [
                'current_page' => $guru->currentPage(),
                'last_page' => $guru->lastPage(),
                'per_page' => $guru->perPage(),
                'total' => $guru->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuruRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Determine role (default: guru)
            $role = $request->input('role', 'guru');
            if (!in_array($role, ['guru', 'wali-kelas', 'kepala-sekolah'])) {
                $role = 'guru';
            }

            // Create user first
            $user = User::create([
                'name' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'role' => $role,
                'is_active' => $request->is_active ?? true,
            ]);

            // Create guru
            $guru = Guru::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'url_photo' => $request->url_photo,
                'url_cover' => $request->url_cover,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil ditambahkan',
                'data' => new GuruResource($guru),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data guru',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diambil',
            'data' => new GuruResource($guru),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuruRequest $request, string $id): JsonResponse
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Update user if exists
            if ($guru->user) {
                $userData = [
                    'name' => $request->nama ?? $guru->user->name,
                    'is_active' => $request->is_active ?? $guru->user->is_active,
                ];

                if ($request->has('username')) {
                    $userData['username'] = $request->username;
                }

                if ($request->has('email')) {
                    $userData['email'] = $request->email;
                }

                if ($request->has('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                if ($request->has('role') && in_array($request->role, ['guru', 'wali-kelas', 'kepala-sekolah'])) {
                    $userData['role'] = $request->role;
                }

                $guru->user->update($userData);
            }

            // Update guru
            $guru->update([
                'nip' => $request->nip ?? $guru->nip,
                'nama' => $request->nama ?? $guru->nama,
                'jenis_kelamin' => $request->jenis_kelamin ?? $guru->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir ?? $guru->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir ?? $guru->tanggal_lahir,
                'alamat' => $request->alamat ?? $guru->alamat,
                'no_hp' => $request->no_hp ?? $guru->no_hp,
                'url_photo' => $request->url_photo ?? $guru->url_photo,
                'url_cover' => $request->url_cover ?? $guru->url_cover,
                'is_active' => $request->is_active ?? $guru->is_active,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil diperbarui',
                'data' => new GuruResource($guru->fresh()),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data guru',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Soft delete guru
            $guru->delete();

            // Optionally soft delete user as well
            if ($guru->user) {
                $guru->user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data guru',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

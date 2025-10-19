<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\User;
use App\Http\Requests\StoreOrangTuaRequest;
use App\Http\Requests\UpdateOrangTuaRequest;
use App\Http\Resources\OrangTuaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrangTuaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = OrangTua::with('user');

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by nama or no_hp
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 15);
        $orangTua = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data orang tua berhasil diambil',
            'data' => OrangTuaResource::collection($orangTua),
            'meta' => [
                'current_page' => $orangTua->currentPage(),
                'last_page' => $orangTua->lastPage(),
                'per_page' => $orangTua->perPage(),
                'total' => $orangTua->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrangTuaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Create user first
            $user = User::create([
                'name' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'role' => 'orang-tua',
                'is_active' => $request->is_active ?? true,
            ]);

            // Create orang tua
            $orangTua = OrangTua::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'pendidikan' => $request->pendidikan,
                'pekerjaan' => $request->pekerjaan,
                'penghasilan' => $request->penghasilan,
                'url_photo' => $request->url_photo,
                'url_cover' => $request->url_cover,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data orang tua berhasil ditambahkan',
                'data' => new OrangTuaResource($orangTua->load('user')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data orang tua',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $orangTua = OrangTua::with('user', 'siswa')->find($id);

        if (!$orangTua) {
            return response()->json([
                'success' => false,
                'message' => 'Data orang tua tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data orang tua berhasil diambil',
            'data' => new OrangTuaResource($orangTua),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrangTuaRequest $request, string $id): JsonResponse
    {
        $orangTua = OrangTua::find($id);

        if (!$orangTua) {
            return response()->json([
                'success' => false,
                'message' => 'Data orang tua tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Update user if exists
            if ($orangTua->user) {
                $userData = [
                    'name' => $request->nama ?? $orangTua->user->name,
                    'is_active' => $request->is_active ?? $orangTua->user->is_active,
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

                $orangTua->user->update($userData);
            }

            // Update orang tua
            $orangTua->update([
                'nama' => $request->nama ?? $orangTua->nama,
                'jenis_kelamin' => $request->jenis_kelamin ?? $orangTua->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir ?? $orangTua->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir ?? $orangTua->tanggal_lahir,
                'alamat' => $request->alamat ?? $orangTua->alamat,
                'no_hp' => $request->no_hp ?? $orangTua->no_hp,
                'pendidikan' => $request->pendidikan ?? $orangTua->pendidikan,
                'pekerjaan' => $request->pekerjaan ?? $orangTua->pekerjaan,
                'penghasilan' => $request->penghasilan ?? $orangTua->penghasilan,
                'url_photo' => $request->url_photo ?? $orangTua->url_photo,
                'url_cover' => $request->url_cover ?? $orangTua->url_cover,
                'is_active' => $request->is_active ?? $orangTua->is_active,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data orang tua berhasil diperbarui',
                'data' => new OrangTuaResource($orangTua->fresh('user')),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data orang tua',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $orangTua = OrangTua::find($id);

        if (!$orangTua) {
            return response()->json([
                'success' => false,
                'message' => 'Data orang tua tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Soft delete orang tua
            $orangTua->delete();

            // Optionally soft delete user as well
            if ($orangTua->user) {
                $orangTua->user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data orang tua berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data orang tua',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

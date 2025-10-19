<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Resources\SiswaResource;
use App\Http\Resources\HafalanResource;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Get hafalan by siswa (On Demand - Step 2)
     * This is called when user clicks "Lihat Detail"
     */
    public function getHafalan($siswaId): JsonResponse
    {
        $siswa = Siswa::with(['kelas:id,nama,ruangan'])
            ->find($siswaId);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        // Get hafalan with guru info, ordered by latest
        $hafalan = $siswa->hafalan()
            ->with('guru:id,nama,nip')
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data hafalan siswa berhasil diambil',
            'data' => [
                'siswa' => new SiswaResource($siswa),
                'hafalan' => HafalanResource::collection($hafalan),
                'statistics' => [
                    'total' => $hafalan->count(),
                    'lancar' => $hafalan->where('status', 'lancar')->count(),
                    'perlu_bimbingan' => $hafalan->where('status', 'perlu bimbingan')->count(),
                    'mengulang' => $hafalan->where('status', 'mengulang')->count(),
                ]
            ]
        ]);
    }

    /**
     * Get siswa statistics
     */
    public function getStatistics($siswaId): JsonResponse
    {
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        $totalHafalan = $siswa->hafalan()->count();
        $statusCounts = $siswa->hafalan()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Get unique surah count
        $uniqueSurah = $siswa->hafalan()
            ->distinct('surah_id')
            ->count('surah_id');

        // Get total ayat
        $totalAyat = $siswa->hafalan()
            ->sum(DB::raw('(ayat_sampai - ayat_dari + 1)'));

        return response()->json([
            'success' => true,
            'message' => 'Statistik siswa berhasil diambil',
            'data' => [
                'siswa' => $siswa->only(['id', 'nis', 'nama']),
                'statistics' => [
                    'total_hafalan' => $totalHafalan,
                    'total_surah' => $uniqueSurah,
                    'total_ayat' => $totalAyat,
                    'lancar' => $statusCounts['lancar'] ?? 0,
                    'perlu_bimbingan' => $statusCounts['perlu bimbingan'] ?? 0,
                    'mengulang' => $statusCounts['mengulang'] ?? 0,
                ]
            ]
        ]);
    }

    /**
     * Get all siswa (with optional filters)
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'search' => 'sometimes|string|max:255',
                'kelas_id' => 'sometimes|integer|exists:kelas,id',
                'sort_by' => 'sometimes|in:nama,nis,tanggal_lahir',
                'sort_order' => 'sometimes|in:asc,desc',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Siswa::with(['kelas:id,nama,ruangan']);

            if ($request->filled('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'nama');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $siswa = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diambil',
                'data' => SiswaResource::collection($siswa),
                'meta' => [
                    'current_page' => $siswa->currentPage(),
                    'total' => $siswa->total(),
                    'per_page' => $siswa->perPage(),
                    'last_page' => $siswa->lastPage(),
                ],
                'links' => [
                    'first' => $siswa->url(1),
                    'last' => $siswa->url($siswa->lastPage()),
                    'prev' => $siswa->previousPageUrl(),
                    'next' => $siswa->nextPageUrl(),
                ]
            ]);
    }

    /**
     * Get siswa detail
     */
    public function show($id): JsonResponse
    {
        $siswa = Siswa::with(['kelas:id,nama,ruangan'])
            ->find($id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail siswa berhasil diambil',
            'data' => new SiswaResource($siswa)
        ]);
    }

    /**
     * Create new siswa (Only for tata-usaha, admin, super-admin)
     */
    public function store(StoreSiswaRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create user account first
            $user = \App\Models\User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password ?? 'password123'),
                'role' => 'siswa',
                'is_active' => true
            ]);

            // Create siswa record
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'nisn' => $request->nisn,
                'nis' => $request->nis,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'nama_ayah' => $request->nama_ayah,
                'nama_ibu' => $request->nama_ibu,
                'no_hp_ortu' => $request->no_hp_ortu,
                'kelas_id' => $request->kelas_id,
                'tahun_masuk' => $request->tahun_masuk,
                'url_photo' => $request->url_photo,
                'url_cover' => $request->url_cover,
                'is_active' => $request->is_active ?? true
            ]);

            DB::commit();

            $siswa->load(['user', 'kelas']);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil dibuat',
                'data' => new SiswaResource($siswa)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update siswa (Only for tata-usaha, admin, super-admin)
     */
    public function update(UpdateSiswaRequest $request, string $id): JsonResponse
    {
        try {
            $siswa = Siswa::with('user')->find($id);

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Update siswa data
            $siswa->update($request->only([
                'nama', 'nisn', 'nis', 'jenis_kelamin', 'tempat_lahir',
                'tanggal_lahir', 'alamat', 'no_hp', 'nama_ayah', 'nama_ibu',
                'no_hp_ortu', 'kelas_id', 'tahun_masuk', 'url_photo',
                'url_cover', 'is_active'
            ]));

            // Update user data if provided
            if ($siswa->user && ($request->has('username') || $request->has('email') || $request->has('password'))) {
                $userData = [];
                if ($request->filled('username')) $userData['username'] = $request->username;
                if ($request->filled('email')) $userData['email'] = $request->email;
                if ($request->filled('password')) $userData['password'] = bcrypt($request->password);
                
                $siswa->user->update($userData);
            }

            DB::commit();

            $siswa->load(['user', 'kelas']);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diupdate',
                'data' => new SiswaResource($siswa)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete siswa (Only for tata-usaha, admin, super-admin)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $siswa = Siswa::with('user')->find($id);

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Soft delete siswa
            $siswa->delete();

            // Also soft delete the user account
            if ($siswa->user) {
                $siswa->user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

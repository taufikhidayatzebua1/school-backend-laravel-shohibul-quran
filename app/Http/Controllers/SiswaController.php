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
        $siswa = Siswa::with(['kelas:id,nama_kelas', 'user:id,email'])
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

        $query = Siswa::with(['kelas:id,nama_kelas', 'user:id,email', 'hafalan.guru:id,nama,nip'])
            ->withCount('hafalan');

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
        if ($request->filled('sort_by')) {
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($request->sort_by, $sortOrder);
        }

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
        $siswa = Siswa::with(['kelas', 'user'])
            ->withCount('hafalan')
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
}

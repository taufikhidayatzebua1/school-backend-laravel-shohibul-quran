<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHafalanRequest;
use App\Http\Requests\UpdateHafalanRequest;
use App\Http\Resources\HafalanResource;
use App\Models\Hafalan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HafalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Eager load relationships to avoid N+1 problem
        // Include siswa.kelas for kelas information
        $query = Hafalan::with([
            'siswa.user:id,email',
            'siswa.kelas:id,nama_kelas,wali_kelas_id,tahun_ajaran',
            'guru:id,user_id,nip,nama,jenis_kelamin,no_hp'
        ]);

        // Filter by kelas_id (optimal way using whereHas)
        if ($request->has('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter by siswa_id
        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        // Filter by guru_id
        if ($request->has('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by surah_id
        if ($request->has('surah_id')) {
            $query->where('surah_id', $request->surah_id);
        }

        // Filter by date range
        if ($request->has('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->has('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $hafalan = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data hafalan berhasil diambil',
            'data' => HafalanResource::collection($hafalan),
            'meta' => [
                'current_page' => $hafalan->currentPage(),
                'total' => $hafalan->total(),
                'per_page' => $hafalan->perPage(),
                'last_page' => $hafalan->lastPage(),
            ],
            'links' => [
                'first' => $hafalan->url(1),
                'last' => $hafalan->url($hafalan->lastPage()),
                'prev' => $hafalan->previousPageUrl(),
                'next' => $hafalan->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHafalanRequest $request): JsonResponse
    {
        // Validation is automatically handled by StoreHafalanRequest
        // No need for duplicate validation here
        $hafalan = Hafalan::create($request->validated());
        $hafalan->load(['siswa', 'guru']);

        return response()->json([
            'success' => true,
            'message' => 'Hafalan berhasil ditambahkan',
            'data' => new HafalanResource($hafalan)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $hafalan = Hafalan::with(['siswa', 'guru'])->find($id);

        if (!$hafalan) {
            return response()->json([
                'success' => false,
                'message' => 'Hafalan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data hafalan berhasil diambil',
            'data' => new HafalanResource($hafalan)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHafalanRequest $request, string $id): JsonResponse
    {
        $hafalan = Hafalan::find($id);

        if (!$hafalan) {
            return response()->json([
                'success' => false,
                'message' => 'Hafalan tidak ditemukan'
            ], 404);
        }

        // Validation is automatically handled by UpdateHafalanRequest
        // No need for duplicate validation here
        $hafalan->update($request->validated());
        $hafalan->load(['siswa', 'guru']);

        return response()->json([
            'success' => true,
            'message' => 'Hafalan berhasil diupdate',
            'data' => new HafalanResource($hafalan)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $hafalan = Hafalan::find($id);

        if (!$hafalan) {
            return response()->json([
                'success' => false,
                'message' => 'Hafalan tidak ditemukan'
            ], 404);
        }

        $hafalan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hafalan berhasil dihapus'
        ]);
    }

    /**
     * Get statistics of hafalan.
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Hafalan::query();

        // Filter by siswa_id if provided
        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        // Filter by guru_id if provided
        if ($request->has('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        $totalHafalan = $query->count();
        $statusCounts = (clone $query)->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return response()->json([
            'success' => true,
            'message' => 'Statistik hafalan berhasil diambil',
            'data' => [
                'total_hafalan' => $totalHafalan,
                'lancar' => $statusCounts['lancar'] ?? 0,
                'perlu_bimbingan' => $statusCounts['perlu bimbingan'] ?? 0,
                'mengulang' => $statusCounts['mengulang'] ?? 0,
            ]
        ]);
    }
}


<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTahunAjaranRequest;
use App\Http\Requests\UpdateTahunAjaranRequest;
use App\Http\Resources\TahunAjaranResource;
use App\Models\TahunAjaran;
use Illuminate\Http\JsonResponse;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tahunAjaran = TahunAjaran::withCount('kelas')
            ->orderByDesc('is_active')
            ->orderByDesc('tahun')
            ->orderByDesc('semester')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data tahun ajaran berhasil diambil',
            'data' => TahunAjaranResource::collection($tahunAjaran)
        ]);
    }

    /**
     * Get active tahun ajaran.
     */
    public function active(): JsonResponse
    {
        $tahunAjaran = TahunAjaran::active()->first();

        if (!$tahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada tahun ajaran yang aktif'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data tahun ajaran aktif berhasil diambil',
            'data' => new TahunAjaranResource($tahunAjaran)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTahunAjaranRequest $request): JsonResponse
    {
        $tahunAjaran = TahunAjaran::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil ditambahkan',
            'data' => new TahunAjaranResource($tahunAjaran)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::withCount('kelas')->find($id);

        if (!$tahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun ajaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tahun ajaran berhasil diambil',
            'data' => new TahunAjaranResource($tahunAjaran)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTahunAjaranRequest $request, string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);

        if (!$tahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun ajaran tidak ditemukan'
            ], 404);
        }

        $tahunAjaran->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil diupdate',
            'data' => new TahunAjaranResource($tahunAjaran)
        ]);
    }

    /**
     * Set tahun ajaran as active.
     */
    public function setActive(string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);

        if (!$tahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun ajaran tidak ditemukan'
            ], 404);
        }

        $tahunAjaran->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil diaktifkan',
            'data' => new TahunAjaranResource($tahunAjaran)
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $tahunAjaran = TahunAjaran::find($id);

        if (!$tahunAjaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun ajaran tidak ditemukan'
            ], 404);
        }

        // Check if this is the active tahun ajaran
        if ($tahunAjaran->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus tahun ajaran yang sedang aktif'
            ], 422);
        }

        $tahunAjaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil dihapus'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\HafalanPublicResource;
use App\Models\Hafalan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicHafalanController extends Controller
{
    /**
     * Get hafalan list for public API (limited data)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Hafalan::with([
            'siswa:id,nis,nama'
        ])->select('id', 'siswa_id', 'surah_id', 'ayat_dari', 'ayat_sampai', 'status', 'tanggal');

        if ($request->has('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'tanggal');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $hafalan = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data hafalan berhasil diambil',
            'data' => HafalanPublicResource::collection($hafalan),
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
     * Get hafalan detail for public API (limited data)
     */
    public function show(string $id): JsonResponse
    {
        $hafalan = Hafalan::with(['siswa:id,nis,nama'])
            ->select('id', 'siswa_id', 'surah_id', 'ayat_dari', 'ayat_sampai', 'status', 'tanggal')
            ->find($id);

        if (!$hafalan) {
            return response()->json([
                'success' => false,
                'message' => 'Hafalan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data hafalan berhasil diambil',
            'data' => new HafalanPublicResource($hafalan)
        ]);
    }
}

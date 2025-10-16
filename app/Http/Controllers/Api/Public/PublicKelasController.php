<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\KelasPublicResource;
use App\Http\Resources\SiswaPublicResource;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicKelasController extends Controller
{
    /**
     * Get all kelas for public API (limited data)
     */
    public function index(): JsonResponse
    {
        $kelas = Kelas::select('id', 'nama_kelas', 'tahun_ajaran')
            ->withCount('siswa')
            ->orderBy('nama_kelas')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diambil',
            'data' => KelasPublicResource::collection($kelas)
        ]);
    }

    /**
     * Get siswa list by kelas for public API (limited data)
     */
    public function getSiswa($kelasId, Request $request): JsonResponse
    {
        $kelas = Kelas::select('id', 'nama_kelas', 'tahun_ajaran')->find($kelasId);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        $siswa = Siswa::where('kelas_id', $kelasId)
            ->select('id', 'nis', 'nama', 'jenis_kelamin', 'kelas_id')
            ->withCount('hafalan')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil',
            'data' => [
                'kelas' => new KelasPublicResource($kelas),
                'siswa' => SiswaPublicResource::collection($siswa)
            ]
        ]);
    }

    /**
     * Get kelas detail for public API (limited data)
     */
    public function show($id): JsonResponse
    {
        $kelas = Kelas::select('id', 'nama_kelas', 'tahun_ajaran')
            ->withCount('siswa')
            ->find($id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kelas berhasil diambil',
            'data' => new KelasPublicResource($kelas)
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Resources\KelasResource;
use App\Http\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KelasController extends Controller
{
    /**
     * Get all kelas with basic info
     */
    public function index(): JsonResponse
    {
        $kelas = Kelas::with(['waliKelas:id,nama,nip', 'tahunAjaran'])
            ->withCount('siswa')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diambil',
            'data' => KelasResource::collection($kelas)
        ]);
    }

    /**
     * Get siswa list by kelas (Lightweight - Step 1)
     * This returns only siswa basic info with hafalan statistics
     */
    public function getSiswa($kelasId, Request $request): JsonResponse
    {
        $kelas = Kelas::with(['waliKelas:id,nama,nip', 'tahunAjaran'])->find($kelasId);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        // Get siswa basic info only
        $siswa = Siswa::where('kelas_id', $kelasId)
            ->select('id', 'nis', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'kelas_id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil',
            'data' => [
                'kelas' => new KelasResource($kelas),
                'siswa' => SiswaResource::collection($siswa)
            ]
        ]);
    }

    /**
     * Get kelas detail
     */
    public function show($id): JsonResponse
    {
        $kelas = Kelas::with(['waliKelas', 'tahunAjaran', 'siswa'])
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
            'data' => new KelasResource($kelas)
        ]);
    }
}

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
        $kelas = Kelas::with('waliKelas:id,nama,nip')
            ->withCount('siswa')
            ->orderBy('nama_kelas')
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
        $kelas = Kelas::with('waliKelas:id,nama,nip')->find($kelasId);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        // Get siswa with hafalan count and statistics
        $siswa = Siswa::where('kelas_id', $kelasId)
            ->with(['user:id,email', 'hafalan.guru:id,nama,nip'])
            ->select('id', 'user_id', 'nis', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'kelas_id')
            ->withCount('hafalan')
            ->get();

        // Add hafalan statistics for each siswa (use already eager loaded hafalan)
        $siswa->each(function ($s) {
            $s->hafalan_stats = [
                'total' => $s->hafalan->count(),
                'lancar' => $s->hafalan->where('status', 'lancar')->count(),
                'perlu_bimbingan' => $s->hafalan->where('status', 'perlu bimbingan')->count(),
                'mengulang' => $s->hafalan->where('status', 'mengulang')->count(),
            ];
            // Get latest hafalan date
            $latestHafalan = $s->hafalan->sortByDesc('tanggal')->first();
            $s->latest_hafalan_date = $latestHafalan ? $latestHafalan->tanggal : null;
        });

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
        $kelas = Kelas::with(['waliKelas', 'siswa'])
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

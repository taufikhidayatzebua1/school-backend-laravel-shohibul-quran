<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiswaPublicResource;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PublicSiswaController extends Controller
{
    /**
     * Get siswa list for public API (limited data)
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
            'search' => 'sometimes|string|max:255',
            'kelas_id' => 'sometimes|integer|exists:kelas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Siswa::with(['kelas:id,nama_kelas'])
            ->select('id', 'nis', 'nama', 'jenis_kelamin', 'kelas_id')
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

        $perPage = $request->get('per_page', 15);
        $siswa = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil',
            'data' => SiswaPublicResource::collection($siswa),
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
     * Get siswa detail for public API (limited data)
     */
    public function show($id): JsonResponse
    {
        $siswa = Siswa::with(['kelas:id,nama_kelas'])
            ->select('id', 'nis', 'nama', 'jenis_kelamin', 'kelas_id')
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
            'data' => new SiswaPublicResource($siswa)
        ]);
    }
}

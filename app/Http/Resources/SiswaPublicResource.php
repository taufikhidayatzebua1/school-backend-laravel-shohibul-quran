<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiswaPublicResource extends JsonResource
{
    /**
     * Transform the resource into an array for public API (limited data).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nis' => $this->nis,
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin,
            'kelas' => [
                'id' => $this->kelas->id ?? null,
                'nama_kelas' => $this->kelas->nama_kelas ?? null,
            ],
            'hafalan_count' => $this->hafalan_count ?? null,
        ];
    }
}

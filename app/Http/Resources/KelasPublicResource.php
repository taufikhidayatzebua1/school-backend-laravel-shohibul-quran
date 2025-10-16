<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KelasPublicResource extends JsonResource
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
            'nama_kelas' => $this->nama_kelas,
            'tahun_ajaran' => $this->tahun_ajaran,
            'siswa_count' => $this->siswa_count ?? null,
        ];
    }
}

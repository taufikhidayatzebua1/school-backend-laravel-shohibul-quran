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
            'tahun_masuk' => $this->tahun_masuk,
            'url_photo' => $this->url_photo,
            'is_active' => $this->is_active,
            'kelas' => [
                'id' => $this->kelas->id ?? null,
                'nama' => $this->kelas->nama ?? null,
                'ruangan' => $this->kelas->ruangan ?? null,
            ],
            'hafalan_count' => $this->hafalan_count ?? null,
        ];
    }
}

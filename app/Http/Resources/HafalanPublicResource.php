<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HafalanPublicResource extends JsonResource
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
            'siswa' => [
                'id' => $this->siswa->id ?? null,
                'nama' => $this->siswa->nama ?? null,
                'nis' => $this->siswa->nis ?? null,
            ],
            'surah_id' => $this->surah_id,
            'ayat_dari' => $this->ayat_dari,
            'ayat_sampai' => $this->ayat_sampai,
            'status' => $this->status,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
        ];
    }
}

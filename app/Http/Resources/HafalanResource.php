<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HafalanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'siswa' => new SiswaResource($this->whenLoaded('siswa')),
            'guru' => $this->when($this->relationLoaded('guru'), function () {
                return [
                    'id' => $this->guru->id,
                    'nama' => $this->guru->nama,
                    'nip' => $this->guru->nip,
                ];
            }),
            'surah_id' => $this->surah_id,
            'ayat_dari' => $this->ayat_dari,
            'ayat_sampai' => $this->ayat_sampai,
            'status' => $this->status,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'keterangan' => $this->keterangan,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

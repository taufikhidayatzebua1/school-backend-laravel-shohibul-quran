<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KelasResource extends JsonResource
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
            'nama' => $this->nama,
            'ruangan' => $this->ruangan,
            'full_name' => $this->full_name,
            'wali_kelas' => new UserResource($this->whenLoaded('waliKelas')),
            'tahun_ajaran' => new TahunAjaranResource($this->whenLoaded('tahunAjaran')),
            'is_active' => $this->is_active ?? null,
            'siswa_count' => $this->siswa_count ?? null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}

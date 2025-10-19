<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'nis' => $this->nis,
            'nama' => $this->nama,
            'tempat_lahir' => $this->tempat_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir,
            'alamat' => $this->alamat,
            'no_hp' => $this->no_hp,
            'tahun_masuk' => $this->tahun_masuk,
            'url_photo' => $this->url_photo,
            'url_cover' => $this->url_cover,
            'is_active' => $this->is_active,
            'kelas' => new KelasResource($this->whenLoaded('kelas')),
            'hafalan_count' => $this->hafalan_count ?? null,
            'hafalan_stats' => $this->hafalan_stats ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}

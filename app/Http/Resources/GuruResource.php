<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuruResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'username' => $this->user->username,
                    'email' => $this->user->email,
                    'role' => $this->user->role,
                    'is_active' => $this->user->is_active,
                ];
            }),
            'nip' => $this->nip,
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_text' => $this->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->jenis_kelamin === 'P' ? 'Perempuan' : null),
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'tanggal_lahir_formatted' => $this->tanggal_lahir?->format('d F Y'),
            'alamat' => $this->alamat,
            'no_hp' => $this->no_hp,
            'url_photo' => $this->url_photo,
            'url_cover' => $this->url_cover,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
            
            // Relasi hafalan
            'hafalan' => $this->whenLoaded('hafalan', function () {
                return $this->hafalan->map(function ($hafalan) {
                    return [
                        'id' => $hafalan->id,
                        'siswa_id' => $hafalan->siswa_id,
                        'juz' => $hafalan->juz,
                        'halaman' => $hafalan->halaman,
                        'nilai' => $hafalan->nilai,
                    ];
                });
            }),
        ];
    }
}

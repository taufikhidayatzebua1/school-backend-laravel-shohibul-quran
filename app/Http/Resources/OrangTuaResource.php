<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrangTuaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_text' => $this->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->jenis_kelamin === 'P' ? 'Perempuan' : null),
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'tanggal_lahir_formatted' => $this->tanggal_lahir?->format('d F Y'),
            'alamat' => $this->alamat,
            'no_hp' => $this->no_hp,
            'pendidikan' => $this->pendidikan,
            'pekerjaan' => $this->pekerjaan,
            'penghasilan' => $this->penghasilan,
            'penghasilan_formatted' => $this->penghasilan_formatted,
            'url_photo' => $this->url_photo,
            'url_cover' => $this->url_cover,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relasi
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
            
            'siswa' => $this->whenLoaded('siswa', function () {
                return $this->siswa->map(function ($siswa) {
                    return [
                        'id' => $siswa->id,
                        'nis' => $siswa->nis,
                        'nama' => $siswa->nama,
                        'kelas_id' => $siswa->kelas_id,
                    ];
                });
            }),
        ];
    }
}

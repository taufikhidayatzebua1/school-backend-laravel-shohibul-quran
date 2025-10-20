<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $profile = [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'role' => $this->role,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        // Add role-specific data based on user role
        switch ($this->role) {
            case 'siswa':
                $profile['siswa'] = $this->whenLoaded('siswa', function () {
                    if ($this->siswa) {
                        return [
                            'id' => $this->siswa->id,
                            'nis' => $this->siswa->nis,
                            'nama' => $this->siswa->nama,
                            'jenis_kelamin' => $this->siswa->jenis_kelamin,
                            'jenis_kelamin_text' => $this->siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->siswa->jenis_kelamin === 'P' ? 'Perempuan' : null),
                            'tempat_lahir' => $this->siswa->tempat_lahir,
                            'tanggal_lahir' => $this->siswa->tanggal_lahir?->format('Y-m-d'),
                            'alamat' => $this->siswa->alamat,
                            'no_hp' => $this->siswa->no_hp,
                            'tahun_masuk' => $this->siswa->tahun_masuk,
                            'url_photo' => $this->siswa->url_photo,
                            'url_cover' => $this->siswa->url_cover,
                            'is_active' => $this->siswa->is_active,
                            'kelas' => $this->siswa->kelas ? [
                                'id' => $this->siswa->kelas->id,
                                'nama' => $this->siswa->kelas->nama,
                                'ruangan' => $this->siswa->kelas->ruangan,
                                'tingkat' => $this->siswa->kelas->tingkat,
                            ] : null,
                        ];
                    }
                    return null;
                });
                break;

            case 'orang-tua':
                $profile['orang_tua'] = $this->whenLoaded('orangTua', function () {
                    if ($this->orangTua) {
                        return [
                            'id' => $this->orangTua->id,
                            'nama' => $this->orangTua->nama,
                            'jenis_kelamin' => $this->orangTua->jenis_kelamin,
                            'jenis_kelamin_text' => $this->orangTua->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->orangTua->jenis_kelamin === 'P' ? 'Perempuan' : null),
                            'tempat_lahir' => $this->orangTua->tempat_lahir,
                            'tanggal_lahir' => $this->orangTua->tanggal_lahir?->format('Y-m-d'),
                            'alamat' => $this->orangTua->alamat,
                            'no_hp' => $this->orangTua->no_hp,
                            'pendidikan' => $this->orangTua->pendidikan,
                            'pekerjaan' => $this->orangTua->pekerjaan,
                            'penghasilan' => $this->orangTua->penghasilan,
                            'penghasilan_formatted' => $this->orangTua->penghasilan_formatted,
                            'url_photo' => $this->orangTua->url_photo,
                            'url_cover' => $this->orangTua->url_cover,
                            'is_active' => $this->orangTua->is_active,
                        ];
                    }
                    return null;
                });
                break;

            case 'guru':
            case 'wali-kelas':
            case 'kepala-sekolah':
                $profile['guru'] = $this->whenLoaded('guru', function () {
                    if ($this->guru) {
                        return [
                            'id' => $this->guru->id,
                            'nip' => $this->guru->nip,
                            'nama' => $this->guru->nama,
                            'jenis_kelamin' => $this->guru->jenis_kelamin,
                            'jenis_kelamin_text' => $this->guru->jenis_kelamin === 'L' ? 'Laki-laki' : ($this->guru->jenis_kelamin === 'P' ? 'Perempuan' : null),
                            'tempat_lahir' => $this->guru->tempat_lahir,
                            'tanggal_lahir' => $this->guru->tanggal_lahir?->format('Y-m-d'),
                            'alamat' => $this->guru->alamat,
                            'no_hp' => $this->guru->no_hp,
                            'url_photo' => $this->guru->url_photo,
                            'url_cover' => $this->guru->url_cover,
                            'is_active' => $this->guru->is_active,
                        ];
                    }
                    return null;
                });
                break;

            default:
                // For roles: tata-usaha, yayasan, admin, super-admin
                // No additional profile data
                break;
        }

        return $profile;
    }
}

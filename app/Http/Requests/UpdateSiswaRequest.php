<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|nullable|exists:users,id|unique:siswa,user_id,' . $this->siswa,
            'nis' => 'sometimes|nullable|string|max:20|unique:siswa,nis,' . $this->siswa,
            'nama' => 'sometimes|nullable|string|max:255',
            'tempat_lahir' => 'sometimes|nullable|string|max:255',
            'jenis_kelamin' => 'sometimes|nullable|in:L,P',
            'tanggal_lahir' => 'sometimes|nullable|date',
            'alamat' => 'sometimes|nullable|string',
            'no_hp' => 'sometimes|nullable|string|max:20',
            'tahun_masuk' => 'sometimes|nullable|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'url_photo' => 'sometimes|nullable|url|max:500',
            'url_cover' => 'sometimes|nullable|url|max:500',
            'is_active' => 'sometimes|nullable|boolean',
            'kelas_id' => 'sometimes|nullable|exists:kelas,id',
        ];
    }
}

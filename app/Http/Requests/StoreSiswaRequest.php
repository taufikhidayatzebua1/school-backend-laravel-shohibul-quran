<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id|unique:siswa,user_id',
            'nis' => 'nullable|string|max:20|unique:siswa',
            'nama' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'tahun_masuk' => 'nullable|digits:4|integer|min:2000|max:' . (date('Y') + 1),
            'url_photo' => 'nullable|url|max:500',
            'url_cover' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
            'kelas_id' => 'nullable|exists:kelas,id',
        ];
    }
}

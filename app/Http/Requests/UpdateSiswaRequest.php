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
            'user_id' => 'sometimes|exists:users,id',
            'nis' => 'sometimes|string|max:20|unique:siswa,nis,' . $this->siswa,
            'nama' => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_lahir' => 'sometimes|date',
            'alamat' => 'sometimes|string',
            'kelas_id' => 'sometimes|exists:kelas,id',
        ];
    }
}

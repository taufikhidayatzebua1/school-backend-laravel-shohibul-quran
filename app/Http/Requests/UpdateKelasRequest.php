<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => 'sometimes|string|max:255',
            'wali_kelas_id' => 'sometimes|exists:users,id',
            'tahun_ajaran' => 'sometimes|string|max:9',
        ];
    }
}

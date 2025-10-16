<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => 'required|string|max:255',
            'wali_kelas_id' => 'required|exists:users,id',
            'tahun_ajaran' => 'required|string|max:9',
        ];
    }
}

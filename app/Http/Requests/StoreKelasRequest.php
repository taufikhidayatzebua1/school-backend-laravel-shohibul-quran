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
            'nama' => 'required|string|max:255',
            'ruangan' => 'nullable|string|max:100',
            'wali_kelas_id' => 'nullable|exists:guru,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama kelas',
            'ruangan' => 'ruangan',
            'wali_kelas_id' => 'wali kelas',
            'tahun_ajaran_id' => 'tahun ajaran',
            'is_active' => 'status aktif',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHafalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => 'sometimes|exists:siswa,id',
            'guru_id' => 'sometimes|exists:guru,id',
            'surah_id' => 'sometimes|integer|min:1|max:114',
            'ayat_dari' => 'sometimes|integer|min:1',
            'ayat_sampai' => 'sometimes|integer|min:1|gte:ayat_dari',
            'status' => 'sometimes|in:lancar,perlu_bimbingan,mengulang',
            'tanggal' => 'sometimes|date',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'surah_id.max' => 'Surah ID tidak boleh lebih dari 114',
            'ayat_sampai.gte' => 'Ayat sampai harus lebih besar atau sama dengan ayat dari',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHafalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => 'required|exists:siswa,id',
            'guru_id' => 'required|exists:guru,id',
            'surah_id' => 'required|integer|min:1|max:114',
            'ayat_dari' => 'required|integer|min:1',
            'ayat_sampai' => 'required|integer|min:1|gte:ayat_dari',
            'status' => 'required|in:lancar,perlu_bimbingan,mengulang',
            'tanggal' => 'required|date',
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

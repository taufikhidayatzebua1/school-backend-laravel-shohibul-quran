<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTahunAjaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'semester' => 'sometimes|string|in:Ganjil,Genap',
            'tahun' => 'sometimes|string|regex:/^\d{4}\/\d{4}$/',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'semester' => 'semester',
            'tahun' => 'tahun ajaran',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'semester.in' => 'Semester harus berisi Ganjil atau Genap.',
            'tahun.regex' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025).',
        ];
    }
}

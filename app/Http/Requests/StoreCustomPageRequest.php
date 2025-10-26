<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CustomPage;

class StoreCustomPageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization akan dihandle di Controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $availableRoles = CustomPage::getAvailableRoles();

        return [
            'title' => 'required|string|max:255',
            'html_content' => 'required|string',
            'role' => 'required|array|min:1',
            'role.*' => 'required|string|in:' . implode(',', $availableRoles),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul halaman wajib diisi.',
            'title.max' => 'Judul halaman maksimal 255 karakter.',
            'html_content.required' => 'Konten HTML wajib diisi.',
            'role.required' => 'Role yang dapat melihat halaman ini wajib diisi.',
            'role.array' => 'Role harus berupa array.',
            'role.min' => 'Minimal 1 role harus dipilih.',
            'role.*.required' => 'Setiap role harus memiliki nilai.',
            'role.*.in' => 'Role yang dipilih tidak valid. Role yang tersedia: siswa, orang-tua, guru, wali-kelas, kepala-sekolah, tata-usaha, yayasan, admin, super-admin.',
        ];
    }
}

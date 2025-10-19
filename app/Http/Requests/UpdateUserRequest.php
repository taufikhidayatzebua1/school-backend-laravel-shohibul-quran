<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $this->user . '|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $this->user,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'role.in' => 'Role yang dipilih tidak valid. Pilihan: siswa, orang-tua, guru, wali-kelas, kepala-sekolah, tata-usaha, yayasan, admin, super-admin.',
        ];
    }
}

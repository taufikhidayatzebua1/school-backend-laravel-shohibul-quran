<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
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

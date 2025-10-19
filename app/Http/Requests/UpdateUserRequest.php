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
            'role' => 'sometimes|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
            'is_active' => 'sometimes|boolean',
        ];
    }
}

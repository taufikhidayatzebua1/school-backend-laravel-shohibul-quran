<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization akan dihandle di Policy dan Controller
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     * Validasi tambahan untuk cek apakah user bisa create dengan role tertentu
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $targetRole = $this->input('role', 'siswa');

            // Jika ada user yang login, cek permission
            if ($user) {
                $canCreateWithRole = false;

                // Tata-usaha hanya bisa create role terbatas
                if ($user->isTataUsaha()) {
                    $canCreateWithRole = in_array($targetRole, ['siswa', 'orang-tua', 'guru', 'wali-kelas']);
                    if (!$canCreateWithRole) {
                        $validator->errors()->add(
                            'role',
                            'Tata-usaha hanya dapat membuat user dengan role: siswa, orang-tua, guru, wali-kelas.'
                        );
                    }
                }
                // Admin bisa create semua kecuali admin dan super-admin
                elseif ($user->isAdmin()) {
                    $canCreateWithRole = !in_array($targetRole, ['admin', 'super-admin']);
                    if (!$canCreateWithRole) {
                        $validator->errors()->add(
                            'role',
                            'Admin tidak dapat membuat user dengan role: admin atau super-admin.'
                        );
                    }
                }
                // Super-admin bisa create semua kecuali super-admin
                elseif ($user->isSuperAdmin()) {
                    $canCreateWithRole = $targetRole !== 'super-admin';
                    if (!$canCreateWithRole) {
                        $validator->errors()->add(
                            'role',
                            'Super-admin tidak dapat membuat user dengan role: super-admin.'
                        );
                    }
                }
                // Role lain tidak bisa create user
                else {
                    $validator->errors()->add(
                        'role',
                        'Anda tidak memiliki izin untuk membuat user baru.'
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'role.required' => 'Role wajib diisi.',
            'role.in' => 'Role yang dipilih tidak valid. Pilihan: siswa, orang-tua, guru, wali-kelas, kepala-sekolah, tata-usaha, yayasan, admin, super-admin.',
        ];
    }
}

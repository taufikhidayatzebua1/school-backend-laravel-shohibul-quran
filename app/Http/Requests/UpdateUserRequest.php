<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\User as UserModel;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization akan dihandle di Policy dan Controller
        return true;
    }

    public function rules(): array
    {
        // Ambil user ID dari route parameter
        $userId = $this->route('user');
        
        return [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $userId . '|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|in:siswa,orang-tua,guru,wali-kelas,kepala-sekolah,tata-usaha,yayasan,admin,super-admin',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     * Validasi tambahan untuk cek permission update
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $authenticatedUser = $this->user();
            $targetUserId = $this->route('user');
            $targetUser = UserModel::find($targetUserId);

            if (!$targetUser) {
                return; // User not found akan di-handle di controller
            }

            $isSelfUpdate = $authenticatedUser->id === $targetUser->id;

            // Validasi update role
            if ($this->has('role')) {
                $newRole = $this->input('role');
                
                // User tidak bisa mengubah role dirinya sendiri
                if ($isSelfUpdate) {
                    $validator->errors()->add(
                        'role',
                        'Anda tidak dapat mengubah role Anda sendiri.'
                    );
                    return;
                }

                // Cek permission berdasarkan role authenticated user
                $canUpdateRole = false;

                if ($authenticatedUser->isTataUsaha()) {
                    $canUpdateRole = in_array($newRole, ['siswa', 'orang-tua', 'guru', 'wali-kelas']);
                    if (!$canUpdateRole) {
                        $validator->errors()->add(
                            'role',
                            'Tata-usaha hanya dapat mengubah role menjadi: siswa, orang-tua, guru, wali-kelas.'
                        );
                    }
                } elseif ($authenticatedUser->isAdmin()) {
                    $canUpdateRole = !in_array($newRole, ['admin', 'super-admin']);
                    if (!$canUpdateRole) {
                        $validator->errors()->add(
                            'role',
                            'Admin tidak dapat mengubah role menjadi: admin atau super-admin.'
                        );
                    }
                } elseif ($authenticatedUser->isSuperAdmin()) {
                    $canUpdateRole = $newRole !== 'super-admin';
                    if (!$canUpdateRole) {
                        $validator->errors()->add(
                            'role',
                            'Super-admin tidak dapat mengubah role menjadi: super-admin.'
                        );
                    }
                } else {
                    $validator->errors()->add(
                        'role',
                        'Anda tidak memiliki izin untuk mengubah role user.'
                    );
                }
            }

            // Validasi update is_active
            if ($this->has('is_active') && $isSelfUpdate) {
                $validator->errors()->add(
                    'is_active',
                    'Anda tidak dapat mengubah status aktif Anda sendiri.'
                );
            }

            // Validasi permission untuk user non-admin yang mencoba update user lain
            if (!$isSelfUpdate && !$authenticatedUser->hasAnyRole(['tata-usaha', 'admin', 'super-admin'])) {
                $validator->errors()->add(
                    'general',
                    'Anda tidak memiliki izin untuk mengubah data user lain.'
                );
            }
        });
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

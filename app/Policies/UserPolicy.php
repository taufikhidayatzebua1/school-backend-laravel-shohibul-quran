<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     * Hanya tata-usaha, admin, dan super-admin yang bisa melihat daftar user
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['tata-usaha', 'admin', 'super-admin']);
    }

    /**
     * Determine if the user can view the user.
     * Hanya tata-usaha, admin, dan super-admin yang bisa melihat detail user
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasAnyRole(['tata-usaha', 'admin', 'super-admin']);
    }

    /**
     * Determine if the user can create users.
     * Hanya tata-usaha, admin, dan super-admin yang bisa create user
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['tata-usaha', 'admin', 'super-admin']);
    }

    /**
     * Determine if the user can update the user.
     * User bisa update dirinya sendiri (data pribadi)
     * Tata-usaha, admin, super-admin bisa update user lain dengan batasan role
     */
    public function update(User $user, User $model): bool
    {
        // User bisa update dirinya sendiri (untuk data pribadi seperti nama, email, password)
        if ($user->id === $model->id) {
            return true;
        }

        // Hanya tata-usaha, admin, super-admin yang bisa update user lain
        return $user->hasAnyRole(['tata-usaha', 'admin', 'super-admin']);
    }

    /**
     * Determine if the user can delete the user.
     * Hanya admin dan super-admin yang bisa delete user
     * Tidak bisa delete diri sendiri
     */
    public function delete(User $user, User $model): bool
    {
        // Tidak bisa delete diri sendiri
        if ($user->id === $model->id) {
            return false;
        }

        // Hanya admin dan super-admin yang bisa delete
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine if the user can restore the user.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * Determine if the user can permanently delete the user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can create a user with the given role.
     * 
     * Aturan:
     * - tata-usaha: hanya bisa create siswa, orang-tua, guru, wali-kelas
     * - admin: bisa create semua kecuali admin dan super-admin
     * - super-admin: bisa create semua kecuali super-admin
     */
    public function createWithRole(User $user, string $targetRole): bool
    {
        // Tata-usaha hanya bisa create role terbatas
        if ($user->isTataUsaha()) {
            return in_array($targetRole, ['siswa', 'orang-tua', 'guru', 'wali-kelas']);
        }

        // Admin bisa create semua kecuali admin dan super-admin
        if ($user->isAdmin()) {
            return !in_array($targetRole, ['admin', 'super-admin']);
        }

        // Super-admin bisa create semua kecuali super-admin
        if ($user->isSuperAdmin()) {
            return $targetRole !== 'super-admin';
        }

        return false;
    }

    /**
     * Determine if the user can update another user's role.
     * 
     * Aturan:
     * - User TIDAK BISA mengubah role dirinya sendiri (mencegah privilege escalation)
     * - tata-usaha: hanya bisa update role siswa, orang-tua, guru, wali-kelas
     * - admin: bisa update semua role kecuali admin dan super-admin
     * - super-admin: bisa update semua role kecuali super-admin
     */
    public function updateRole(User $user, User $model, string $newRole): bool
    {
        // User tidak bisa mengubah role dirinya sendiri
        if ($user->id === $model->id) {
            return false;
        }

        // Tata-usaha hanya bisa update role terbatas
        if ($user->isTataUsaha()) {
            return in_array($newRole, ['siswa', 'orang-tua', 'guru', 'wali-kelas']);
        }

        // Admin bisa update semua role kecuali admin dan super-admin
        if ($user->isAdmin()) {
            return !in_array($newRole, ['admin', 'super-admin']);
        }

        // Super-admin bisa update semua role kecuali super-admin
        if ($user->isSuperAdmin()) {
            return $newRole !== 'super-admin';
        }

        return false;
    }

    /**
     * Determine if the user can update specific fields of another user.
     * 
     * Aturan untuk update field:
     * - User bisa update data pribadi dirinya sendiri (name, email, password)
     * - User TIDAK BISA mengubah role dan is_active dirinya sendiri
     * - Tata-usaha, admin, super-admin bisa update user lain sesuai permission
     */
    public function updateField(User $user, User $model, string $field): bool
    {
        // User update dirinya sendiri
        if ($user->id === $model->id) {
            // Bisa update data pribadi
            if (in_array($field, ['name', 'username', 'email', 'password'])) {
                return true;
            }
            
            // Tidak bisa update role dan is_active dirinya sendiri
            if (in_array($field, ['role', 'is_active'])) {
                return false;
            }
        }

        // Tata-usaha, admin, super-admin bisa update user lain
        return $user->hasAnyRole(['tata-usaha', 'admin', 'super-admin']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'html_content',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Check if a specific role can view this page
     *
     * @param string $role
     * @return bool
     */
    public function canBeViewedByRole(string $role): bool
    {
        return in_array($role, $this->role ?? []);
    }

    /**
     * Check if user can view this page
     *
     * @param User $user
     * @return bool
     */
    public function canBeViewedByUser(User $user): bool
    {
        return $this->canBeViewedByRole($user->role);
    }

    /**
     * Scope to get pages viewable by specific role
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewableByRole($query, string $role)
    {
        return $query->whereJsonContains('role', $role);
    }

    /**
     * Scope to get pages viewable by user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeViewableByUser($query, User $user)
    {
        return $query->viewableByRole($user->role);
    }

    /**
     * Get available roles from users table enum
     *
     * @return array
     */
    public static function getAvailableRoles(): array
    {
        return [
            'siswa',
            'orang-tua',
            'guru',
            'wali-kelas',
            'kepala-sekolah',
            'tata-usaha',
            'yayasan',
            'admin',
            'super-admin'
        ];
    }
}

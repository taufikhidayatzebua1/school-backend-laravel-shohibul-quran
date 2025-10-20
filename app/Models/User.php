<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'role' => 'siswa',
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is admin (not including super-admin)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is super-admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    /**
     * Check if user is siswa
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Check if user is orang-tua
     */
    public function isOrangTua(): bool
    {
        return $this->role === 'orang-tua';
    }

    /**
     * Check if user is guru
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Check if user is wali kelas
     */
    public function isWaliKelas(): bool
    {
        return $this->role === 'wali-kelas';
    }

    /**
     * Check if user is kepala sekolah
     */
    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala-sekolah';
    }

    /**
     * Check if user is tata usaha
     */
    public function isTataUsaha(): bool
    {
        return $this->role === 'tata-usaha';
    }

    /**
     * Check if user is yayasan
     */
    public function isYayasan(): bool
    {
        return $this->role === 'yayasan';
    }

    /**
     * Check if user can create a user with the given role
     * 
     * Aturan:
     * - tata-usaha: hanya bisa create siswa, orang-tua, guru, wali-kelas
     * - admin: bisa create semua kecuali admin dan super-admin
     * - super-admin: bisa create semua kecuali super-admin
     */
    public function canCreateRole(string $targetRole): bool
    {
        if ($this->isTataUsaha()) {
            return in_array($targetRole, ['siswa', 'orang-tua', 'guru', 'wali-kelas']);
        }

        if ($this->isAdmin()) {
            return !in_array($targetRole, ['admin', 'super-admin']);
        }

        if ($this->isSuperAdmin()) {
            return $targetRole !== 'super-admin';
        }

        return false;
    }

    /**
     * Check if user can update another user's role
     * 
     * Aturan:
     * - User TIDAK BISA mengubah role dirinya sendiri
     * - tata-usaha: hanya bisa update role siswa, orang-tua, guru, wali-kelas
     * - admin: bisa update semua role kecuali admin dan super-admin
     * - super-admin: bisa update semua role kecuali super-admin
     */
    public function canUpdateRole(User $targetUser, string $newRole): bool
    {
        // User tidak bisa mengubah role dirinya sendiri
        if ($this->id === $targetUser->id) {
            return false;
        }

        if ($this->isTataUsaha()) {
            return in_array($newRole, ['siswa', 'orang-tua', 'guru', 'wali-kelas']);
        }

        if ($this->isAdmin()) {
            return !in_array($newRole, ['admin', 'super-admin']);
        }

        if ($this->isSuperAdmin()) {
            return $newRole !== 'super-admin';
        }

        return false;
    }

    /**
     * Get allowed roles for this user to create/assign
     */
    public function getAllowedRoles(): array
    {
        if ($this->isTataUsaha()) {
            return ['siswa', 'orang-tua', 'guru', 'wali-kelas'];
        }

        if ($this->isAdmin()) {
            return ['siswa', 'orang-tua', 'guru', 'wali-kelas', 'kepala-sekolah', 'tata-usaha', 'yayasan'];
        }

        if ($this->isSuperAdmin()) {
            return ['siswa', 'orang-tua', 'guru', 'wali-kelas', 'kepala-sekolah', 'tata-usaha', 'yayasan', 'admin'];
        }

        return [];
    }

    /**
     * Get the siswa record associated with the user.
     * 
     * One-to-One relationship
     */
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    /**
     * Get the guru record associated with the user.
     * 
     * One-to-One relationship
     */
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    /**
     * Get the orang tua record associated with the user.
     * 
     * One-to-One relationship
     */
    public function orangTua()
    {
        return $this->hasOne(OrangTua::class);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Generate unique username
     * Format: name_randomstring (e.g., john_doe_a1b2c3)
     */
    public static function generateUniqueUsername(string $name): string
    {
        // Clean name: lowercase, replace spaces with underscore, remove special chars
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $name)));
        
        // Limit base username to 20 characters
        $baseUsername = substr($baseUsername, 0, 20);
        
        // Try base username first
        $username = $baseUsername;
        $counter = 1;
        
        // If exists, add random suffix
        while (self::where('username', $username)->exists()) {
            $randomSuffix = substr(md5(uniqid(rand(), true)), 0, 6);
            $username = $baseUsername . '_' . $randomSuffix;
            
            // Safety: after 10 attempts, use timestamp
            if ($counter > 10) {
                $username = $baseUsername . '_' . time();
                break;
            }
            $counter++;
        }
        
        return $username;
    }

    /**
     * Boot method to auto-generate username if not provided
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = self::generateUniqueUsername($user->name);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tahun_ajaran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'semester',
        'tahun',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Ketika menyimpan (create atau update)
        static::saving(function ($tahunAjaran) {
            if ($tahunAjaran->is_active) {
                // Set semua tahun ajaran lain menjadi tidak aktif
                static::where('id', '!=', $tahunAjaran->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    /**
     * Get the kelas for the tahun ajaran.
     */
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    /**
     * Scope a query to only include active tahun ajaran.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get full name of tahun ajaran (Semester Tahun).
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->semester} {$this->tahun}";
    }
}

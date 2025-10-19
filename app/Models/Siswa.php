<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'siswa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'tempat_lahir',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'tahun_masuk',
        'url_photo',
        'url_cover',
        'is_active',
        'kelas_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the siswa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the kelas that owns the siswa.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Get the hafalan for the siswa.
     */
    public function hafalan(): HasMany
    {
        return $this->hasMany(Hafalan::class);
    }

    /**
     * Get the nama lengkap attribute (accessor for compatibility).
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama ?? '';
    }
}


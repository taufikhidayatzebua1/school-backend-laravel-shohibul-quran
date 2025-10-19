<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrangTua extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orang_tua';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'pendidikan',
        'pekerjaan',
        'penghasilan',
        'url_photo',
        'url_cover',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'penghasilan' => 'decimal:2',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the orang tua.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the siswa (children) for the orang tua.
     * Note: You may need to create a pivot table if one orang tua can have multiple siswa
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'orang_tua_id');
    }

    /**
     * Get the nama lengkap attribute (accessor for compatibility).
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama ?? '';
    }

    /**
     * Get the formatted penghasilan attribute.
     */
    public function getPenghasilanFormattedAttribute(): string
    {
        if ($this->penghasilan) {
            return 'Rp ' . number_format($this->penghasilan, 0, ',', '.');
        }
        return '-';
    }
}

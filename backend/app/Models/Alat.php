<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    protected $table = 'alat';
    protected $fillable = [
    'kategori_id', 'nama_alat', 'stok', 'status_kondisi', 'deskripsi', 'gambar'
    ];

    protected function casts(): array {
        return [
            'stok' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->gambar)) {
            return asset('images/no-image.svg');
        }

        $gambar = $this->gambar;

        if (str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
            return $gambar;
        }

        $cleanPath = ltrim($gambar, '/');
        $normalized = preg_replace('#^storage/#', '', $cleanPath);

        if ($normalized && file_exists(public_path('storage/' . $normalized))) {
            return asset('storage/' . $normalized);
        }

        if ($normalized && file_exists(public_path($normalized))) {
            return asset($normalized);
        }

        if ($cleanPath && file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return asset('images/no-image.svg');
    }

    public function kategori(): BelongsTo {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPinjam(): HasMany {
        return $this->hasMany(DetailPinjam::class);
    }

    public function scopeTersedia($query) 
    { 
        return $query->where('stok', '>', 0)->where('status_kondisi', 'Baik'); 
    } 
}
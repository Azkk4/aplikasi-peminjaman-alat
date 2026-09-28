<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';
    protected $fillable = [ 
        'user_id', 'aktivitas' 
    ];
    
    public function user(): BelongsTo { 
        return $this->belongsTo(User::class); 
    } 

    public function getDisplayActivityAttribute(): string
    {
        if (! preg_match('/\b(peminjaman|pengembalian|alat|user)\s+ID\s*#?\s*(\d+)/iu', $this->aktivitas, $matches)) {
            return $this->aktivitas;
        }

        [$phrase, $type, $id] = $matches;
        $identity = match (strtolower($type)) {
            'peminjaman' => Peminjaman::with('user')->find($id)?->user?->name,
            'pengembalian' => $this->returnOwnerName($id),
            'alat' => Alat::find($id)?->nama_alat,
            'user' => User::find($id)?->name,
            default => null,
        };

        if (! $identity) {
            return $this->aktivitas;
        }

        $replacement = match (strtolower($type)) {
            'peminjaman' => "peminjaman milik {$identity}",
            'pengembalian' => "pengembalian milik {$identity}",
            'alat' => "alat \"{$identity}\"",
            'user' => "user \"{$identity}\"",
        };

        return str_replace($phrase, $replacement, $this->aktivitas);
    }

    private function returnOwnerName(string $id): ?string
    {
        $peminjamanId = DB::table('pengembalian')->where('id', $id)->value('peminjaman_id');

        return $peminjamanId
            ? Peminjaman::with('user')->find($peminjamanId)?->user?->name
            : null;
    }
}

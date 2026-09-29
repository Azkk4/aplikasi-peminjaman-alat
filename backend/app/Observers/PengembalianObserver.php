<?php

namespace App\Observers;

use App\Models\Pengembalian;
use App\Models\LogAktivitas; 
use Illuminate\Support\Facades\Auth; 

class PengembalianObserver
{
    private function catatLog(string $pesan): void 
    { 
        if (Auth::check()) { 
            LogAktivitas::create([ 
                'user_id' => Auth::id(), 
                'aktivitas' => $pesan, 
            ]); 
        } 
    } 
 
    public function created(Pengembalian $pengembalian): void 
    { 
        $pengembalian->loadMissing('peminjaman.user', 'peminjaman.detailPinjam.alat');
        $nama = $pengembalian->peminjaman?->user?->name ?? 'User tidak tersedia';
        $alat = $pengembalian->peminjaman?->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') ?: 'Alat tidak tersedia';
        $this->catatLog("Memproses pengembalian {$alat} milik {$nama}");
    } 
 
    public function updated(Pengembalian $pengembalian): void 
    { 
        $perubahan = array_diff(array_keys($pengembalian->getChanges()), ['updated_at']); 
 
        if (!empty($perubahan)) { 
            $pengembalian->loadMissing('peminjaman.user', 'peminjaman.detailPinjam.alat');
            $nama = $pengembalian->peminjaman?->user?->name ?? 'User tidak tersedia';
            $alat = $pengembalian->peminjaman?->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') ?: 'Alat tidak tersedia';
            $detail = [];

            if (in_array('kondisi_kembali', $perubahan, true)) {
                $detail[] = "kondisi menjadi {$pengembalian->kondisi_kembali}";
            }
            if (in_array('denda', $perubahan, true)) {
                $detail[] = 'denda menjadi Rp ' . number_format($pengembalian->denda, 0, ',', '.');
            }

            $this->catatLog("Merevisi pengembalian {$alat} milik {$nama}: " . implode(', ', $detail) . '.');
        } 
    } 
 
    public function deleted(Pengembalian $pengembalian): void 
    { 
        $pengembalian->loadMissing('peminjaman.user', 'peminjaman.detailPinjam.alat');
        $nama = $pengembalian->peminjaman?->user?->name ?? 'User tidak tersedia';
        $this->catatLog("Menghapus riwayat pengembalian milik {$nama}.");
    }

    /**
     * Handle the Pengembalian "restored" event.
     */
    public function restored(Pengembalian $pengembalian): void
    {
        //
    }

    /**
     * Handle the Pengembalian "force deleted" event.
     */
    public function forceDeleted(Pengembalian $pengembalian): void
    {
        //
    }
}

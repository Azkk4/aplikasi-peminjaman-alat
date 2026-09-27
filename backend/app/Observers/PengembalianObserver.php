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
            $kolom = implode(', ', $perubahan); 
            $this->catatLog("Merevisi data pengembalian (Kolom diubah: {$kolom})");
        } 
    } 
 
    public function deleted(Pengembalian $pengembalian): void 
    { 
        $this->catatLog('Membatalkan/menghapus riwayat pengembalian');
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

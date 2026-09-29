<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas; 
use Illuminate\Support\Facades\Auth; 

class PeminjamanObserver
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

    public function created(Peminjaman $peminjaman): void
    { 
        $namaPeminjam = $peminjaman->user?->name ?? 'User'; 
        $this->catatLog("Peminjam ({$namaPeminjam}) membuat permohonan peminjaman baru");
    } 
 
    public function updated(Peminjaman $peminjaman): void 
    { 
        $namaPeminjam = $peminjaman->user?->name ?? 'User tidak tersedia';
        if ($peminjaman->wasChanged('status')) { 
            $aktivitas = match ($peminjaman->status) {
                'dipinjam' => "Menyetujui permohonan peminjaman milik {$namaPeminjam}.",
                'ditolak' => "Menolak permohonan peminjaman milik {$namaPeminjam}.",
                'selesai' => "Menyelesaikan pengembalian peminjaman milik {$namaPeminjam}.",
                default => "Status peminjaman milik {$namaPeminjam} berubah menjadi '{$peminjaman->status}'.",
            };
            $this->catatLog($aktivitas);
        } else { 
            if (!empty($peminjaman->getChanges())) { 
                $this->catatLog("Memperbarui detail peminjaman milik {$namaPeminjam}");
            } 
        } 
    } 
 
    public function deleted(Peminjaman $peminjaman): void 
    { 
        $namaPeminjam = $peminjaman->user?->name ?? 'User tidak tersedia';
        $this->catatLog("Membatalkan/menghapus permohonan peminjaman milik {$namaPeminjam}");
    }

    /**
     * Handle the Peminjaman "restored" event.
     */
    public function restored(Peminjaman $peminjaman): void
    {
        //
    }

    /**
     * Handle the Peminjaman "force deleted" event.
     */
    public function forceDeleted(Peminjaman $peminjaman): void
    {
        //
    }
}

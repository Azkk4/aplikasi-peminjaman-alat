<?php

namespace App\Observers;

use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class KategoriObserver
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

    public function created(Kategori $kategori): void
    {
        $this->catatLog("Menambahkan kategori {$kategori->nama_kategori}.");
    }

    public function updated(Kategori $kategori): void
    {
        if ($kategori->wasChanged('nama_kategori')) {
            $sebelumnya = $kategori->getOriginal('nama_kategori');
            $this->catatLog("Mengubah kategori {$sebelumnya} menjadi {$kategori->nama_kategori}.");
        }
    }

    public function deleted(Kategori $kategori): void
    {
        $this->catatLog("Menghapus kategori {$kategori->nama_kategori}.");
    }
}
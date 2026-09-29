<?php

namespace App\Observers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
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

    public function created(User $user): void
    {
        $this->catatLog("Menambahkan pengguna {$user->name} dengan role {$user->role}.");
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['name', 'role'])) {
            $this->catatLog("Memperbarui data pengguna {$user->name} (role: {$user->role}).");
        }
    }

    public function deleted(User $user): void
    {
        $this->catatLog("Menghapus pengguna {$user->name}.");
    }
}
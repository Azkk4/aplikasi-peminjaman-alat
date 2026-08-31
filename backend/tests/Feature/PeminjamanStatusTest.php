<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_selesai_is_accepted_by_peminjaman_table(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'secret123',
            'role' => 'peminjam',
            'no_hp' => '081234567890',
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay()->setTime(23, 59, 59),
            'status' => 'selesai',
        ]);

        $this->assertSame('selesai', $peminjaman->status);
        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => 'selesai',
        ]);
    }
}

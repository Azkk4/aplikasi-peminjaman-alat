<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogActivityDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_peminjaman_ids_resolve_to_owner_name_and_missing_rows_keep_original_text(): void
    {
        $user = User::create([
            'name' => 'Fajar Oktavian',
            'email' => 'fajar-log@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'dipinjam',
        ]);
        $legacy = LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => "Menyetujui permohonan peminjaman ID #{$peminjaman->id}.",
        ]);
        $missing = LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => 'Peminjaman ID #999 tidak ditemukan.',
        ]);

        $this->assertSame(
            'Menyetujui permohonan peminjaman milik Fajar Oktavian.',
            $legacy->display_activity
        );
        $this->assertSame('Peminjaman ID #999 tidak ditemukan.', $missing->display_activity);
    }

    public function test_legacy_tool_ids_resolve_to_tool_names(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'tool-log-admin@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);
        $category = Kategori::create(['nama_kategori' => 'Test Category']);
        $tool = Alat::create([
            'kategori_id' => $category->id,
            'nama_alat' => 'Multimeter Digital',
            'stok' => 1,
            'status_kondisi' => 'Baik',
        ]);
        $log = LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => "Menghapus alat ID #{$tool->id}.",
        ]);

        $this->assertSame('Menghapus alat "Multimeter Digital".', $log->display_activity);
    }

    public function test_model_activity_logs_are_registered_and_use_related_names(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'activity-admin@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        $category = Kategori::create(['nama_kategori' => 'Perkakas']);
        $borrower = User::create([
            'name' => 'Fajar Oktavian',
            'email' => 'activity-borrower@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $tool = Alat::create([
            'kategori_id' => $category->id,
            'nama_alat' => 'Multimeter Digital',
            'stok' => 1,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $borrower->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'diajukan',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $tool->id,
            'jumlah' => 1,
        ]);
        $peminjaman->update(['status' => 'dipinjam']);
        $pengembalian = Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => 'Baik',
            'denda' => 0,
            'petugas_id' => $admin->id,
        ]);
        $pengembalian->update(['kondisi_kembali' => 'Rusak Ringan', 'denda' => 2500]);

        $activities = LogAktivitas::pluck('aktivitas')->all();

        $this->assertContains('Menambahkan kategori Perkakas.', $activities);
        $this->assertContains('Menambahkan pengguna Fajar Oktavian dengan role peminjam.', $activities);
        $this->assertContains('Menyetujui permohonan peminjaman milik Fajar Oktavian.', $activities);
        $this->assertContains('Memproses pengembalian Multimeter Digital milik Fajar Oktavian', $activities);
        $this->assertContains('Merevisi pengembalian Multimeter Digital milik Fajar Oktavian: kondisi menjadi Rusak Ringan, denda menjadi Rp 2.500.', $activities);
    }
}

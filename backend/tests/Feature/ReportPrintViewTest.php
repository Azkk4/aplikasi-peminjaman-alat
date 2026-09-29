<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPrintViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_report_renders_a_standalone_print_document(): void
    {
        $petugas = User::create([
            'name' => 'Petugas Laporan',
            'email' => 'report-petugas@example.test',
            'password' => 'password123',
            'role' => 'petugas',
        ]);
        $peminjam = User::create([
            'name' => 'Fajar Oktavian',
            'email' => 'report-borrower@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Alat Uji']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Multimeter Digital',
            'stok' => 1,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => '2026-09-10 09:30:00',
            'tgl_kembali_plan' => '2026-09-15 23:59:59',
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);

        $this->actingAs($petugas)
            ->get(route('petugas.laporan.index', [
                'tgl_mulai' => '2026-09-10',
                'tgl_selesai' => '2026-09-15',
                'status' => 'dipinjam',
            ]))
            ->assertOk()
            ->assertSee('Laporan Transaksi Peminjaman')
            ->assertSee('10/09/2026 09:30')
            ->assertSee('15/09/2026')
            ->assertSee('Fajar Oktavian')
            ->assertSee('Multimeter Digital')
            ->assertSee('2 unit')
            ->assertSee('Dipinjam')
            ->assertSee('id="mobile-menu"', false)
            ->assertSee('Logout')
            ->assertSee('size: A4 landscape')
            ->assertSee('report-actions, .report-filters', false);
    }
}
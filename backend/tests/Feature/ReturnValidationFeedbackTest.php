<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnValidationFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_return_form_renders_required_field_error(): void
    {
        [$petugas, , $peminjaman] = $this->createBorrowing();

        $this->actingAs($petugas)
            ->from(route('petugas.pengembalian.index'))
            ->followingRedirects()
            ->post(route('petugas.pengembalian.proses', $peminjaman), [
                '_return_id' => $peminjaman->id,
                'kondisi_kembali' => '',
                'denda' => '0',
            ])
            ->assertOk()
            ->assertSee('kondisi pengembalian wajib diisi.');

        $this->assertDatabaseMissing('pengembalian', ['peminjaman_id' => $peminjaman->id]);
    }

    public function test_peminjam_return_form_renders_required_field_error(): void
    {
        [, $borrower, $peminjaman] = $this->createBorrowing();

        $this->actingAs($borrower)
            ->from(route('peminjam.peminjaman.show', $peminjaman))
            ->followingRedirects()
            ->post(route('peminjam.peminjaman.return', $peminjaman), ['kondisi_kembali' => ''])
            ->assertOk()
            ->assertSee('kondisi pengembalian wajib diisi.');

        $this->assertDatabaseMissing('pengembalian', ['peminjaman_id' => $peminjaman->id]);
    }

    private function createBorrowing(): array
    {
        $petugas = User::create([
            'name' => 'Test Petugas',
            'email' => 'petugas-return-validation@example.test',
            'password' => 'password123',
            'role' => 'petugas',
        ]);
        $borrower = User::create([
            'name' => 'Test Borrower',
            'email' => 'borrower-return-validation@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $category = Kategori::create(['nama_kategori' => 'Return Test Category']);
        $alat = Alat::create([
            'kategori_id' => $category->id,
            'nama_alat' => 'Return Test Tool',
            'stok' => 0,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $borrower->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        return [$petugas, $borrower, $peminjaman];
    }
}

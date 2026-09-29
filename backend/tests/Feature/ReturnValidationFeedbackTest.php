<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnValidationFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_return_form_renders_required_field_error(): void
    {
        [$petugas, , $peminjaman] = $this->createBorrowing();
        $peminjaman->update(['pengembalian_diajukan_at' => now()]);

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

    public function test_peminjam_request_does_not_set_condition_or_process_return(): void
    {
        [, $borrower, $peminjaman, $alat] = $this->createBorrowing();

        $this->actingAs($borrower)
            ->from(route('peminjam.peminjaman.show', $peminjaman))
            ->followingRedirects()
            ->post(route('peminjam.peminjaman.return', $peminjaman), [
                'kondisi_kembali' => 'Rusak Berat',
                'denda' => '999999',
                'status' => 'selesai',
            ])
            ->assertOk()
            ->assertSee('Pengajuan pengembalian berhasil dikirim kepada petugas.');

        $this->assertDatabaseMissing('pengembalian', ['peminjaman_id' => $peminjaman->id]);
        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => 'dipinjam',
        ]);
        $this->assertNotNull($peminjaman->fresh()->pengembalian_diajukan_at);
        $this->assertSame(0, $alat->fresh()->stok);
    }

    public function test_only_the_borrower_can_request_their_own_return(): void
    {
        [$petugas, , $peminjaman, $alat] = $this->createBorrowing();
        $otherBorrower = User::create([
            'name' => 'Other Borrower',
            'email' => 'other-borrower-return@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->actingAs($otherBorrower)
            ->post(route('peminjam.peminjaman.return', $peminjaman))
            ->assertForbidden();

        $this->assertNull($peminjaman->fresh()->pengembalian_diajukan_at);
        $this->assertDatabaseMissing('pengembalian', ['peminjaman_id' => $peminjaman->id]);
        $this->assertSame(0, $alat->fresh()->stok);
    }

    public function test_staff_cannot_accept_a_return_with_an_unknown_condition(): void
    {
        [$petugas, , $peminjaman, $alat] = $this->createBorrowing();
        $peminjaman->update(['pengembalian_diajukan_at' => now()]);

        $this->actingAs($petugas)
            ->from(route('petugas.pengembalian.index'))
            ->post(route('petugas.pengembalian.proses', $peminjaman), [
                'kondisi_kembali' => 'Perlu Diperiksa',
                'denda' => '0',
            ])
            ->assertRedirect(route('petugas.pengembalian.index'))
            ->assertSessionHasErrors('kondisi_kembali');

        $this->assertDatabaseMissing('pengembalian', ['peminjaman_id' => $peminjaman->id]);
        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id, 'status' => 'dipinjam']);
        $this->assertSame(0, $alat->fresh()->stok);
    }

    public function test_staff_acceptance_finalizes_return_and_restores_stock_only_once(): void
    {
        [$petugas, , $peminjaman, $alat] = $this->createBorrowing();
        $peminjaman->update([
            'status' => 'telat',
            'pengembalian_diajukan_at' => now(),
        ]);

        $payload = [
            '_return_id' => $peminjaman->id,
            'kondisi_kembali' => 'Rusak Ringan',
            'denda' => '5000',
        ];

        $this->actingAs($petugas)
            ->post(route('petugas.pengembalian.proses', $peminjaman), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Rusak Ringan',
            'denda' => 5000,
        ]);
        $this->assertSame(1, $alat->fresh()->stok);

        $this->actingAs($petugas)
            ->post(route('petugas.pengembalian.proses', $peminjaman), $payload)
            ->assertRedirect();

        $this->assertSame(1, $alat->fresh()->stok);
        $this->assertDatabaseCount('pengembalian', 1);
    }

    public function test_admin_can_edit_existing_return_condition_and_fine_without_changing_stock_or_status(): void
    {
        [$petugas, , $peminjaman, $alat] = $this->createBorrowing();
        $peminjaman->update(['status' => 'selesai']);
        $pengembalian = Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => 'Baik',
            'denda' => 0,
            'petugas_id' => $petugas->id,
        ]);
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-return-edit@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.pengembalian.update', $pengembalian), [
                'kondisi_kembali' => 'Rusak Berat',
                'denda' => 10000,
            ])
            ->assertRedirect(route('admin.pengembalian.index'));

        $this->assertDatabaseHas('pengembalian', [
            'id' => $pengembalian->id,
            'kondisi_kembali' => 'Rusak Berat',
            'denda' => 10000,
        ]);
        $this->assertDatabaseCount('pengembalian', 1);
        $this->assertSame('selesai', $peminjaman->fresh()->status);
        $this->assertSame(0, $alat->fresh()->stok);
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

        return [$petugas, $borrower, $peminjaman, $alat];
    }
}

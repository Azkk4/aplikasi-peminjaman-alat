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

class PeminjamanDeletionRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_pending_application_can_be_deleted(): void
    {
        $admin = $this->createUser('admin', 'delete-admin@example.test');
        $peminjaman = $this->createPeminjaman('diajukan');

        $this->actingAs($admin)
            ->delete(route('admin.peminjaman.destroy', $peminjaman))
            ->assertRedirect(route('admin.peminjaman.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }

    public function test_pending_application_with_details_is_preserved_with_notification(): void
    {
        $admin = $this->createUser('admin', 'delete-details-admin@example.test');
        $peminjaman = $this->createPeminjaman('diajukan');
        $this->addBorrowedTool($peminjaman);

        $this->actingAs($admin)
            ->from(route('admin.peminjaman.index'))
            ->delete(route('admin.peminjaman.destroy', $peminjaman))
            ->assertRedirect(route('admin.peminjaman.index'))
            ->assertSessionHas('error', 'Peminjaman tidak dapat dihapus karena memiliki detail alat. Histori transaksi tetap dipertahankan.');

        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id]);
        $this->assertDatabaseHas('detail_pinjam', ['peminjaman_id' => $peminjaman->id]);
    }

    public function test_rejected_and_processed_applications_are_preserved(): void
    {
        $admin = $this->createUser('admin', 'delete-processed-admin@example.test');

        foreach (['ditolak', 'dipinjam', 'telat', 'selesai'] as $status) {
            $peminjaman = $this->createPeminjaman($status);

            $this->actingAs($admin)
                ->from(route('admin.peminjaman.index'))
                ->delete(route('admin.peminjaman.destroy', $peminjaman))
                ->assertRedirect(route('admin.peminjaman.index'))
                ->assertSessionHas('error', 'Hanya pengajuan yang belum diproses yang dapat dihapus.');

            $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id, 'status' => $status]);
        }
    }

    public function test_application_with_return_record_is_preserved(): void
    {
        $admin = $this->createUser('admin', 'delete-return-admin@example.test');
        $peminjam = $this->createUser('peminjam', 'delete-return-borrower@example.test');
        $petugas = $this->createUser('petugas', 'delete-return-staff@example.test');
        $peminjaman = $this->createPeminjaman('diajukan', $peminjam->id);
        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => 'Baik',
            'denda' => 0,
            'petugas_id' => $petugas->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.peminjaman.index'))
            ->delete(route('admin.peminjaman.destroy', $peminjaman))
            ->assertRedirect(route('admin.peminjaman.index'))
            ->assertSessionHas('error', 'Peminjaman tidak dapat dihapus karena memiliki data pengembalian.');

        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id]);
        $this->assertDatabaseHas('pengembalian', ['peminjaman_id' => $peminjaman->id]);
    }

    private function createUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role) . ' User',
            'email' => $email,
            'password' => 'password123',
            'role' => $role,
        ]);
    }

    private function createPeminjaman(string $status, ?int $userId = null): Peminjaman
    {
        $userId ??= $this->createUser('peminjam', uniqid('borrower-', true) . '@example.test')->id;

        return Peminjaman::create([
            'user_id' => $userId,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => $status,
        ]);
    }

    private function addBorrowedTool(Peminjaman $peminjaman): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Delete Rules Category']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Delete Rules Tool',
            'stok' => 0,
            'status_kondisi' => 'Baik',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);
    }
}
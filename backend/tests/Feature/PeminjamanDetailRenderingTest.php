<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanDetailRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_and_history_details_render_once_inside_one_app_shell(): void
    {
        $user = User::create([
            'name' => 'Borrower Detail',
            'email' => 'borrower-detail@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $kategori = Kategori::create(['nama_kategori' => 'Kategori Detail']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Alat Detail',
            'stok' => 1,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'tgl_pinjam' => now(),
            'tgl_kembali_plan' => now()->addDay(),
            'status' => 'dipinjam',
        ]);
        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);

        foreach ([false, true] as $fromHistory) {
            if ($fromHistory) {
                $peminjaman->update(['status' => 'selesai']);
            }

            $response = $this->actingAs($user)
                ->get(route('peminjam.peminjaman.show', array_filter([
                    'peminjaman' => $peminjaman->id,
                    'from' => $fromHistory ? 'riwayat' : null,
                ])))
                ->assertOk();

            $html = $response->getContent();
            $this->assertSame(1, substr_count($html, 'id="mobile-menu"'));
            $this->assertSame(1, substr_count($html, 'Pengajuan #' . $peminjaman->id));
            $this->assertSame(1, substr_count($html, 'Alat yang dipinjam'));

            if (! $fromHistory) {
                $response->assertSee('data-confirm="true"', false)
                    ->assertDontSee("confirm('Yakin ingin mengembalikan peminjaman ini?')", false)
                    ->assertDontSee('Kondisi alat saat dikembalikan');
            }
        }
    }
}
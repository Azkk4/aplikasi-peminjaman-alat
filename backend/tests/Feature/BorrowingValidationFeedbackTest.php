<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingValidationFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_malformed_borrowing_arrays_return_field_errors_without_server_error(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-loan-validation@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);
        $borrower = User::create([
            'name' => 'Test Borrower',
            'email' => 'borrower-loan-validation@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $category = Kategori::create(['nama_kategori' => 'Test Category']);
        Alat::create([
            'kategori_id' => $category->id,
            'nama_alat' => 'Test Tool',
            'stok' => 3,
            'status_kondisi' => 'Baik',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.peminjaman.create'))
            ->followingRedirects()
            ->post(route('admin.peminjaman.store'), [
                'user_id' => $borrower->id,
                'tgl_pinjam' => now()->toDateString(),
                'tgl_kembali_plan' => now()->addDay()->toDateString(),
                'alat_id' => 'bukan-array',
                'jumlah' => 'bukan-array',
            ])
            ->assertOk()
            ->assertSee('alat harus berupa daftar data yang valid.')
            ->assertSee('jumlah harus berupa daftar data yang valid.');
    }
}

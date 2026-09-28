<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_create_and_edit_persist_and_duplicate_errors_render(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'category-admin@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kategori.store'), ['nama_kategori' => 'Perangkat Audio'])
            ->assertRedirect(route('admin.kategori.index'));

        $category = Kategori::where('nama_kategori', 'Perangkat Audio')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.kategori.create'))
            ->followingRedirects()
            ->post(route('admin.kategori.store'), ['nama_kategori' => 'Perangkat Audio'])
            ->assertOk()
            ->assertSee('nama kategori sudah digunakan.')
            ->assertSee('value="Perangkat Audio"', false);

        $this->actingAs($admin)
            ->put(route('admin.kategori.update', $category), ['nama_kategori' => 'Perangkat Suara'])
            ->assertRedirect(route('admin.kategori.index'));

        $this->assertDatabaseHas('kategori', ['id' => $category->id, 'nama_kategori' => 'Perangkat Suara']);
        $this->assertDatabaseCount('kategori', 1);
    }
}
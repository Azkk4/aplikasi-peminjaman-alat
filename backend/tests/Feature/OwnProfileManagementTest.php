<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_can_edit_only_its_own_personal_profile(): void
    {
        foreach ([
            ['role' => 'peminjam', 'super_admin' => false],
            ['role' => 'petugas', 'super_admin' => false],
            ['role' => 'admin', 'super_admin' => false],
            ['role' => 'admin', 'super_admin' => true],
        ] as $index => $account) {
            $user = User::create([
                'name' => 'Profile User ' . $index,
                'email' => "profile-role-{$index}@example.test",
                'password' => 'password123',
                'role' => $account['role'],
                'is_super_admin' => $account['super_admin'],
                'is_active' => true,
            ]);
            $routePrefix = $account['role'];

            $this->actingAs($user)
                ->get(route($routePrefix . '.profil'))
                ->assertOk()
                ->assertSee('data-crop-input="foto_profile"', false)
                ->assertDontSee('name="role"', false)
                ->assertDontSee('name="is_active"', false)
                ->assertDontSee('name="is_super_admin"', false);

            $this->actingAs($user)
                ->put(route($routePrefix . '.profil.update'), [
                    'name' => 'Updated Profile ' . $index,
                    'email' => "updated-profile-{$index}@example.test",
                    'no_hp' => '12345678901',
                    'alamat' => 'Alamat terbaru',
                    'role' => 'peminjam',
                    'is_active' => false,
                    'is_super_admin' => false,
                    'user_id' => 9999,
                ])
                ->assertRedirect();

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Updated Profile ' . $index,
                'email' => "updated-profile-{$index}@example.test",
                'no_hp' => '12345678901',
                'alamat' => 'Alamat terbaru',
                'role' => $account['role'],
                'is_active' => true,
                'is_super_admin' => $account['super_admin'],
            ]);
            $this->assertDatabaseMissing('users', ['id' => 9999]);
        }
    }
}
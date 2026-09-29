<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_deactivate_and_reactivate_another_user(): void
    {
        $admin = $this->createUser('admin', 'activation-admin@example.test');
        $user = $this->createUser('peminjam', 'activation-user@example.test');
        $this->assertTrue($admin->fresh()->is_active);
        $this->assertTrue($user->fresh()->is_active);

        $this->actingAs($admin)
            ->patch(route('admin.user.status', $user), ['is_active' => false])
            ->assertRedirect(route('admin.user.index'))
            ->assertSessionHas('success', 'User berhasil dinonaktifkan.');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);

        $this->actingAs($admin)
            ->patch(route('admin.user.status', $user), ['is_active' => true])
            ->assertRedirect(route('admin.user.index'))
            ->assertSessionHas('success', 'User berhasil diaktifkan.');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => true]);
    }

    public function test_user_list_shows_status_and_only_activation_actions(): void
    {
        $admin = $this->createUser('admin', 'activation-list-admin@example.test');
        $this->createUser('peminjam', 'activation-list-user@example.test');

        $this->actingAs($admin)
            ->get(route('admin.user.index'))
            ->assertOk()
            ->assertSee('Role / Hak Akses')
            ->assertSee('No. HP')
            ->assertSee('Status')
            ->assertSee('Aktif')
            ->assertSee('Nonaktifkan')
            ->assertDontSee('Hapus');
    }

    public function test_admin_cannot_deactivate_themselves_or_a_super_admin(): void
    {
        $admin = $this->createUser('admin', 'activation-self@example.test');
        $superAdmin = $this->createUser('admin', 'activation-super@example.test', true);

        $this->actingAs($admin)
            ->from(route('admin.user.index'))
            ->patch(route('admin.user.status', $admin), ['is_active' => false])
            ->assertRedirect(route('admin.user.index'))
            ->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun sendiri.');

        $this->actingAs($admin)
            ->from(route('admin.user.index'))
            ->patch(route('admin.user.status', $superAdmin), ['is_active' => false])
            ->assertRedirect(route('admin.user.index'))
            ->assertSessionHas('error', 'Akun Super Admin tidak dapat dinonaktifkan.');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => true]);
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id, 'is_active' => true]);
    }

    public function test_inactive_users_cannot_log_in_or_continue_using_existing_sessions_and_tokens(): void
    {
        $user = $this->createUser('peminjam', 'inactive-login@example.test');
        $user->update(['is_active' => false]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->actingAs($user)
            ->get(route('peminjam.dashboard'))
            ->assertRedirect(route('login'));

        $token = $user->createToken('inactive-test')->plainTextToken;
        $this->withToken($token)
            ->getJson('/api/me')
            ->assertForbidden();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertUnprocessable();
    }

    public function test_legacy_api_delete_deactivates_without_deleting_user_history(): void
    {
        $admin = $this->createUser('admin', 'activation-api-admin@example.test');
        $user = $this->createUser('peminjam', 'activation-api-user@example.test');

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Pengguna berhasil dinonaktifkan.');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
    }

    private function createUser(string $role, string $email, bool $isSuperAdmin = false): User
    {
        return User::create([
            'name' => ucfirst($role) . ' User',
            'email' => $email,
            'password' => 'password123',
            'role' => $role,
            'is_super_admin' => $isSuperAdmin,
        ]);
    }
}
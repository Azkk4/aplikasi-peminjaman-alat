<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_admin_cannot_create_admin_or_change_super_admin(): void
    {
        $admin = User::create([
            'name' => 'Regular Admin',
            'email' => 'regular-admin@example.test',
            'password' => 'password123',
            'role' => 'admin',
            'is_super_admin' => false,
        ]);
        $superAdmin = User::create([
            'name' => 'Protected Admin',
            'email' => 'protected-admin@example.test',
            'password' => 'password123',
            'role' => 'admin',
            'is_super_admin' => true,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Escalated Admin',
                'email' => 'escalated-admin@example.test',
                'password' => 'password123',
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$superAdmin->id}", [
                'name' => 'Changed Name',
                'email' => $superAdmin->email,
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.user.edit', $superAdmin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $superAdmin->id,
            'name' => 'Protected Admin',
            'is_super_admin' => true,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserValidationFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_form_renders_phone_error_and_preserves_the_invalid_value(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-validation@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.user.create'))
            ->followingRedirects()
            ->post(route('admin.user.store'), [
                'name' => 'Test User',
                'email' => 'invalid-phone@example.test',
                'password' => 'password123',
                'role' => 'peminjam',
                'no_hp' => '1234567890',
            ])
            ->assertOk()
            ->assertSee('Nomor HP harus berupa 11 sampai 13 digit angka.')
            ->assertSee('value="1234567890"', false);
    }

    public function test_user_form_saves_phone_numbers_with_11_to_13_digits(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-validation@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        foreach (['12345678901', '123456789012', '1234567890123'] as $index => $phoneNumber) {
            $email = "valid-phone-{$index}@example.test";

            $this->actingAs($admin)
                ->post(route('admin.user.store'), [
                    'name' => 'Valid User',
                    'email' => $email,
                    'password' => 'password123',
                    'role' => 'peminjam',
                    'no_hp' => $phoneNumber,
                ])
                ->assertRedirect(route('admin.user.index'));

            $this->assertDatabaseHas('users', ['email' => $email, 'no_hp' => $phoneNumber]);
        }
    }

    public function test_user_edit_saves_phone_numbers_with_11_to_13_digits(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin-edit-validation@example.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);
        $user = User::create([
            'name' => 'Existing User',
            'email' => 'existing-user@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        foreach (['12345678901', '123456789012', '1234567890123'] as $phoneNumber) {
            $this->actingAs($admin)
                ->put(route('admin.user.update', $user), [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'no_hp' => $phoneNumber,
                ])
                ->assertRedirect(route('admin.user.index'));

            $this->assertDatabaseHas('users', ['id' => $user->id, 'no_hp' => $phoneNumber]);
        }
    }

    public function test_profile_saves_phone_numbers_with_11_to_13_digits(): void
    {
        $user = User::create([
            'name' => 'Profile User',
            'email' => 'profile-validation@example.test',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        foreach (['12345678901', '123456789012', '1234567890123'] as $phoneNumber) {
            $this->actingAs($user)
                ->put(route('peminjam.profil.update'), [
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_hp' => $phoneNumber,
                ])
                ->assertRedirect();

            $this->assertDatabaseHas('users', ['id' => $user->id, 'no_hp' => $phoneNumber]);
        }
    }
}
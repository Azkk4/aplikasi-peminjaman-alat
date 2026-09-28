<?php

namespace Tests\Feature;

use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PhoneNumberValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_phone_number_accepts_only_11_to_13_digits(): void
    {
        $rules = ['no_hp' => (new RegisterRequest())->rules()['no_hp']];

        foreach (['1234567890', '12345678901234', '1234567890a', '123-456-78901', '123 456 78901', ' 12345678901', '12345678901 '] as $phoneNumber) {
            $this->assertFalse(
                Validator::make(['no_hp' => $phoneNumber], $rules)->passes(),
                "Nomor HP {$phoneNumber} harus ditolak."
            );
        }

        foreach (['12345678901', '123456789012', '1234567890123'] as $phoneNumber) {
            $this->assertTrue(
                Validator::make(['no_hp' => $phoneNumber], $rules)->passes(),
                "Nomor HP {$phoneNumber} harus diterima."
            );
        }

        $this->assertTrue(Validator::make(['no_hp' => ''], $rules)->passes());
        $this->assertTrue(Validator::make(['no_hp' => null], $rules)->passes());
    }

    public function test_api_registration_returns_localized_phone_validation_error(): void
    {
        foreach (['1234567890', ' 12345678901', '12345678901 '] as $index => $phoneNumber) {
            $this->postJson('/api/register', [
                'name' => 'Test User',
                'email' => "phone-validation-{$index}@example.test",
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'no_hp' => $phoneNumber,
            ])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('no_hp')
                ->assertJsonPath('errors.no_hp.0', 'Nomor HP harus berupa 11 sampai 13 digit angka.');
        }
    }

    public function test_api_registration_saves_11_to_13_digit_phone_numbers(): void
    {
        foreach (['12345678901', '123456789012', '1234567890123'] as $index => $phoneNumber) {
            $email = "valid-api-phone-{$index}@example.test";

            $this->postJson('/api/register', [
                'name' => 'Test User',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'no_hp' => $phoneNumber,
            ])->assertCreated();

            $this->assertDatabaseHas('users', ['email' => $email, 'no_hp' => $phoneNumber]);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_returns_429_after_ten_attempts_for_one_email_from_different_addresses(): void
    {
        $user = User::factory()->create();
        $this->failLoginTenTimesFromDifferentAddresses($user->email);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
            ->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertStatus(429);

        $this->assertGuest();
    }

    public function test_attempts_for_one_email_do_not_throttle_another_email(): void
    {
        $attacked = User::factory()->create();
        $other = User::factory()->create();
        $this->failLoginTenTimesFromDifferentAddresses($attacked->email);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
            ->post('/login', ['email' => $other->email, 'password' => 'password'])
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertAuthenticatedAs($other);
    }

    private function failLoginTenTimesFromDifferentAddresses(string $email): void
    {
        foreach (range(1, 10) as $attempt) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.{$attempt}"])
                ->post('/login', ['email' => $email, 'password' => 'wrong-password'])
                ->assertStatus(302);
        }
    }
}

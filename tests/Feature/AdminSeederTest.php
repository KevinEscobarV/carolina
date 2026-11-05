<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ResetAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_admin_replaces_the_password_of_an_existing_admin(): void
    {
        $admin = User::factory()->create([
            'email' => 'alejandra@loteosmcrv.com',
            'password' => Hash::make('admin2024*'),
        ]);

        $this->artisan('db:seed', ['--class' => ResetAdmin::class])->assertSuccessful();

        $admin->refresh();
        $this->assertFalse(Hash::check('admin2024*', $admin->password));
        $this->assertTrue($admin->hasRole('Administrador'));
    }
}

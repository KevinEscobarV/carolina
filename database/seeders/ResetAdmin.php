<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ResetAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD') ?: Str::password(16);

        $user = User::updateOrCreate(
            ['email' => 'alejandra@loteosmcrv.com'],
            [
                'name' => 'Alejandra Barahona',
                'password' => bcrypt($password),
            ]
        );

        $role = Role::firstOrCreate(['name' => 'Administrador']);

        $user->assignRole($role);

        $this->command->info('Usuario administrador restablecido');

        if (! env('ADMIN_PASSWORD')) {
            $this->command->warn("Contraseña generada: {$password}");
        }
    }
}

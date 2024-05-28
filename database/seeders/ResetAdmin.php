<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ResetAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'alejandra@loteosmcrv.com'],
            [
                'name' => 'Alejandra Barahona',
                'password' => bcrypt('admin2024*'),
            ]
        );

        $role = Role::firstOrCreate(['name' => 'Administrador']);

        $user->assignRole($role);

        $this->command->info('Usuario administrador restablecido');
    }
}

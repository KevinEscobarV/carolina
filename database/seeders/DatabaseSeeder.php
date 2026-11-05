<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $password = env('ADMIN_PASSWORD') ?: Str::password(16);

        User::create([
            'name' => 'Alejandra Barahona',
            'email' => 'alejandra@loteosmcrv.com',
            'password' => bcrypt($password),
        ]);

        if (! env('ADMIN_PASSWORD')) {
            $this->command->warn("Contraseña del administrador: {$password}");
        }

        Category::create([
            'name' => 'Rivarca',
        ]);

        $this->call([
            ImportDataSeeder::class,
            ImportPromises::class,
            ImportPayments::class,
            ImportTransactions::class,
        ]);
    }
}

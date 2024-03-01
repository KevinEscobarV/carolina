<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Buyer;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@rivarca.com',
            'password' => bcrypt('admin'),
        ]);

        Category::create([
            'name' => 'Rivarca',
        ]);

        $this->call([
            ImportDataSeeder::class,
        ]);
    }
}

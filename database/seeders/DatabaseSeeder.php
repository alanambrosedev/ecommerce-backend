<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Sample customer
        User::factory()->create([
            'name'     => 'Customer',
            'email'    => 'customer@example.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
        ]);

        $this->call([SizeSeeder::class, BrandSeeder::class, CategorySeeder::class]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Size::insert([
            ['name' => 'S', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'M', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'XL', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

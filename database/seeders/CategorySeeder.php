<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            "Men's Clothing",
            "Women's Clothing",
            'Footwear',
            'Accessories',
            'Sportswear',
            'Casuals',
            'Formal Wear',
            'Kids',
            'Bags',
            'Watches',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name'   => $name,
                'status' => 1,
            ]);
        }
    }
}

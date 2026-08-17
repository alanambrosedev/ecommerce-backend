<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Nike',
            'Adidas',
            'Puma',
            'Reebok',
            'New Balance',
            'Under Armour',
            'Vans',
            'Converse',
            'Fila',
            "Levi's",
        ];

        foreach ($brands as $name) {
            Brand::create([
                'name' => $name,
                'status' => 1,
            ]);
        }
    }
}

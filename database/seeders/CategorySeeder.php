<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Toys',
            'description' => fake()->text(255),
        ]);

        Category::create([
            'name' => 'Electronics',
            'description' => fake()->text(255),
        ]);

        Category::create([
            'name' => 'Food',
            'description' => fake()->text(255),
        ]);
    }
}

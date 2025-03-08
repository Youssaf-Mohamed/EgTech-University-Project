<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Electronics', 'description' => 'Devices and gadgets'],
            ['category_name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['category_name' => 'Books', 'description' => 'Books and literature'],
            ['category_name' => 'Home & Kitchen', 'description' => 'Appliances and decor'],
            ['category_name' => 'Sports', 'description' => 'Fitness and recreation'],
            ['category_name' => 'Toys', 'description' => 'Games and entertainment'],
            ['category_name' => 'Beauty & Personal Care', 'description' => 'Skincare and cosmetics'],
            ['category_name' => 'Health & Household', 'description' => 'Supplements and household items'],
            ['category_name' => 'Pet Supplies', 'description' => 'Pet food and accessories'],
            ['category_name' => 'Outdoor & Patio', 'description' => 'Gardening and outdoor furniture'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            $randomImage = 'category(' . rand(1, 5) . ').png';

            $category->addMedia(public_path("images/test/{$randomImage}"))
                ->preservingOriginal()
                ->toMediaCollection('category_images');
        }
    }
}

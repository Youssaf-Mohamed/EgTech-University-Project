<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $vendors = Vendor::pluck('id')->toArray();
        $categories = Category::pluck('id')->toArray();

        if (empty($vendors)) {
            $this->command->info('No vendors found. Please seed vendors first.');
            return;
        }

        if (empty($categories)) {
            $this->command->info('No categories found. Please seed categories first.');
            return;
        }

        $products = [];

        for ($i = 1; $i <= 50; $i++) {
            $product = Product::create([
                'vendor_id' => $vendors[array_rand($vendors)],
                'product_name' => 'Product ' . $i,
                'description' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Molestias consequatur dolor architecto omnis fugit ' . $i . ' - ' . Str::random(50),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $product->categories()->attach(array_rand(array_flip($categories), rand(1, 3)));
        }
    }
}

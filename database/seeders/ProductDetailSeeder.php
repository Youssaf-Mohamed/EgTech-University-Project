<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductDetailSeeder extends Seeder
{
    public function run()
    {
        $products = Product::pluck('id')->toArray();

        if (empty($products)) {
            $this->command->info('You have no products yet.');
            return;
        }

        $productDetails = [];

        foreach ($products as $productId) {
            $detailsCount = rand(1, 3);

            for ($i = 0; $i < $detailsCount; $i++) {
                $productDetails[] = [
                    'product_id' => $productId,
                    'size' => ['S', 'M', 'L', 'XL'][array_rand(['S', 'M', 'L', 'XL'])],
                    'color' => ['Red', 'Blue', 'Green', 'Black', 'White'][array_rand(['Red', 'Blue', 'Green', 'Black', 'White'])],
                    'price' => rand(50, 500),
                    'discount' => rand(0, 30),
                    'stock' => rand(5, 100),
                    'material' => ['Cotton', 'Polyester', 'Leather', 'Denim'][array_rand(['Cotton', 'Polyester', 'Leather', 'Denim'])],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        ProductDetail::insert($productDetails);

        foreach (ProductDetail::all() as $productDetail) {
            $imageCount = rand(1, 3);
            for ($j = 0; $j < $imageCount; $j++) {
                $randomImage = 'product(' . rand(1, 6) . ').png';

                $productDetail->addMedia(public_path("images/test/{$randomImage}"))
                    ->preservingOriginal()
                    ->toMediaCollection('product_images');
            }
        }

        $this->command->info('Product details seeded successfully.');
    }
}

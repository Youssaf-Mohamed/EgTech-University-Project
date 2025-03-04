<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('no users or products found');
            return;
        }

        foreach (range(1, 50) as $index) {
            Review::create([
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'rating' => rand(1, 5),
                'comment' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Quod minus, cupiditate distinctio soluta maxime neque' . $index,
            ]);
        }
    }
}

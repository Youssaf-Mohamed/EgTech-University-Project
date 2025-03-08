<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Advertisement;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $advertisements = [
            [
                'title' => 'Special Offer on Electronics',
                'brief' => 'Get up to 50% off on all electronic devices.',
                'content' => 'This is a limited-time offer. Don\'t miss out on our special deals for electronics.',
                'redirect_to' => 'electronics',
                'target_screen' => 'home',
            ],
            [
                'title' => 'Summer Fashion Sale',
                'brief' => 'Enjoy 30% discount on summer collections.',
                'content' => 'Shop now and get amazing discounts on our summer fashion collection.',
                'redirect_to' => 'fashion',
                'target_screen' => 'home',
            ],
            [
                'title' => 'New Year Discounts',
                'brief' => 'Celebrate the new year with exclusive offers.',
                'content' => 'Limited-time discounts on all products. Hurry up and shop now!',
                'redirect_to' => 'new-year-sale',
                'target_screen' => 'home',
            ],
            [
                'title' => 'Free Shipping Worldwide',
                'brief' => 'Get free shipping on all orders above $100.',
                'content' => 'Order now and enjoy free worldwide shipping on all orders exceeding $100.',
                'redirect_to' => 'shipping',
                'target_screen' => 'home',
            ],
            [
                'title' => 'Weekly Deals',
                'brief' => 'New deals every week. Check them out!',
                'content' => 'Stay tuned for our weekly deals and save big on your favorite products.',
                'redirect_to' => 'weekly-deals',
                'target_screen' => 'home',
            ],
        ];

        foreach ($advertisements as $data) {
            // Create the advertisement
            $advertisement = Advertisement::create($data);

            // Add an image to the advertisement
            $advertisement->addMedia(public_path('images/navigation-placeholder.png'))
            ->preservingOriginal()
            ->toMediaCollection('advertisement_image');
        }
    }
}

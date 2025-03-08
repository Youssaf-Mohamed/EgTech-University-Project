<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class VendorSeeder extends Seeder
{
    public function run()
    {
        $vendors = [
            [
                'brand_name' => 'Vendor One',
                'description' => 'This is the first vendor.',
                'phone' => '1234567890',
                'status' => 'active',
            ],
            [
                'brand_name' => 'Vendor Two',
                'description' => 'This is the second vendor.',
                'phone' => '0987654321',
                'status' => 'pending',
            ],
            [
                'brand_name' => 'Vendor Three',
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident laudantium ducimus consequatur laborum. Possimus suscipit quo at culpa perferendis accusamus! Ea modi est pariatur laudantium commodi exercitationem aut, quidem eius.',
                'phone' => '0987654321',
                'status' => 'pending',
            ],
            [
                'brand_name' => 'Vendor Four',
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident laudantium ducimus consequatur laborum. Possimus suscipit quo at culpa perferendis accusamus! Ea modi est pariatur laudantium commodi exercitationem aut, quidem eius.',
                'phone' => '0987654321',
                'status' => 'pending',
            ],
            [
                'brand_name' => 'Vendor Five',
                'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident laudantium ducimus consequatur laborum. Possimus suscipit quo at culpa perferendis accusamus! Ea modi est pariatur laudantium commodi exercitationem aut, quidem eius.',
                'phone' => '0987654321',
                'status' => 'pending',
            ],
        ];

        foreach ($vendors as $vendorData) {
            $vendor = Vendor::create($vendorData);

            $randomImage = 'vendor(' . rand(1, 6) . ').jpg';

            $vendor->addMedia(public_path("images/test/{$randomImage}"))
                ->preservingOriginal()
                ->toMediaCollection('vendor_images');
        }
    }
}

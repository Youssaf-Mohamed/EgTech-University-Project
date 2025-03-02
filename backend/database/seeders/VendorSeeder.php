<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run()
    {
        $vendors = [
            [
                'brand_name' => 'Tech Solutions',
                'image' => null,
                'description' => 'Leading provider of tech services',
                'phone' => '0123456789',
                'status' => 'active',
            ],
            [
                'brand_name' => 'Fashion Hub',
                'image' => null,
                'description' => 'Trendy fashion store',
                'phone' => '0987654321',
                'status' => 'pending',
            ],
            [
                'brand_name' => 'Home Essentials',
                'image' => null,
                'description' => 'Best home products',
                'phone' => '01122334455',
                'status' => 'inactive',
            ]
        ];

        foreach ($vendors as $vendorData) {
            $vendor = Vendor::create($vendorData);

            $regionIds = DB::table('regions')->pluck('id')->take(4)->toArray();
            $vendor->regions()->sync($regionIds);
        }
    }
}

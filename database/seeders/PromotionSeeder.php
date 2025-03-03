<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Promotion;
use App\Models\Vendor;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to prevent constraint issues
        Schema::disableForeignKeyConstraints();
        DB::table('promotions')->truncate();
        DB::table('vendor_promotion')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create promotions
        $promotions = [
            [
                'name' => 'Discount 10%',
                'promotion_amount' => 10.00,
                'promotion_priority' => 1,
                'duration' => Carbon::now()->addDays(30),
                'status' => 'active',
            ],
            [
                'name' => 'Holiday Special',
                'promotion_amount' => 15.50,
                'promotion_priority' => 2,
                'duration' => Carbon::now()->addDays(60),
                'status' => 'active',
            ],
            [
                'name' => 'Summer Deal',
                'promotion_amount' => 20.00,
                'promotion_priority' => 3,
                'duration' => Carbon::now()->addDays(45),
                'status' => 'inactive',
            ],
        ];

        foreach ($promotions as $promotionData) {
            Promotion::create($promotionData);
        }

        // Fetch all vendors
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            Log::warning('No vendors found to assign promotions.');
            return;
        }

        // Assign promotions to vendors
        $promotionIds = Promotion::pluck('id')->toArray();
        foreach ($vendors as $vendor) {
            $vendor->promotions()->attach(
                $promotionIds[array_rand($promotionIds)],
                [
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays(30),
                    'status' => 'pending',
                ]
            );
        }
    }
}

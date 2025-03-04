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
        // تعطيل فحص العلاقات الخارجية مؤقتًا
        Schema::disableForeignKeyConstraints();
        DB::table('promotions')->truncate();
        DB::table('vendor_promotion')->truncate();
        Schema::enableForeignKeyConstraints();

        // إنشاء العروض الترويجية
        $promotions = [
            [
                'name' => 'Discount 10%',
                'promotion_amount' => 10.00,
                'promotion_priority' => 1,
                'duration' => 30, // عدد الأيام
                'status' => 'active',
            ],
            [
                'name' => 'Holiday Special',
                'promotion_amount' => 15.50,
                'promotion_priority' => 2,
                'duration' => 60, // عدد الأيام
                'status' => 'active',
            ],
            [
                'name' => 'Summer Deal',
                'promotion_amount' => 20.00,
                'promotion_priority' => 3,
                'duration' => 45, // عدد الأيام
                'status' => 'inactive',
            ],
        ];

        foreach ($promotions as $promotionData) {
            Promotion::create($promotionData);
        }

        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            Log::warning('No vendors found to assign promotions.');
            return;
        }

        $promotionIds = Promotion::pluck('id', 'duration')->toArray();
        foreach ($vendors as $vendor) {
            $promotionId = array_rand($promotionIds);
            $duration = $promotionIds[$promotionId];

            $vendor->promotions()->attach(
                $promotionId,
                [
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addDays($duration),
                    'status' => 'pending',
                ]
            );
        }
    }
}

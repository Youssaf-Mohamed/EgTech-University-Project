<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $promotions = [
            ['name' => 'Super Sale', 'promotion_amount' => 20, 'promotion_priority' => 1, 'duration' => 30, 'status' => 'active'],
            ['name' => 'Black Friday', 'promotion_amount' => 50, 'promotion_priority' => 2, 'duration' => 15, 'status' => 'active'],
            ['name' => 'New Year Offer', 'promotion_amount' => 25, 'promotion_priority' => 3, 'duration' => 20, 'status' => 'active'],
            ['name' => 'Holiday Special', 'promotion_amount' => 30, 'promotion_priority' => 2, 'duration' => 10, 'status' => 'active'],
        ];

        foreach ($promotions as $promo) {
            Promotion::create($promo);
        }

        $vendors = Vendor::pluck('id')->toArray();
        $promotionIds = Promotion::pluck('id')->toArray();

        if (empty($vendors) || empty($promotionIds)) {
            $this->command->info('No vendors or promotions found.');
            return;
        }

        foreach ($vendors as $vendorId) {
            $selectedPromotions = array_rand($promotionIds, 2);

            foreach ($selectedPromotions as $index) {
                $promotionId = $promotionIds[$index];

                $startDate = Carbon::now()->subDays(rand(0, 10));
                $endDate = $startDate->copy()->addDays(rand(5, 30));

                DB::table('vendor_promotion')->insert([
                    'vendor_id' => $vendorId,
                    'promotion_id' => $promotionId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

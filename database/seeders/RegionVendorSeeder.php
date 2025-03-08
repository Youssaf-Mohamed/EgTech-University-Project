<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use App\Models\Vendor;

class RegionVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = Region::all();
        $vendors = Vendor::all();

        if ($regions->isEmpty() || $vendors->isEmpty()) {
            $this->command->warn('No regions or vendors found. Skipping seeding.');
            return;
        }

        foreach ($vendors as $vendor) {
            $assignedRegions = $regions->random(rand(1, 7));

            foreach ($assignedRegions as $region) {
                DB::table('region_vendor')->insert([
                    'region_id' => $region->id,
                    'vendor_id' => $vendor->id,
                    'delivery_cost' => rand(10, 250),
                    'discount' => rand(0, 25),
                    'description' => ' Vendor ' . $vendor->name . ' is available in ' . $region->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

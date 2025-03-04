<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class UserVendorSeeder extends Seeder
{
    public function run()
    {
        $users = User::pluck('id')->toArray();
        $vendors = Vendor::pluck('id')->toArray();

        if (empty($users) || empty($vendors)) {
            $this->command->info('No users or vendors found.');
            return;
        }

        $userVendorData = [];

        foreach ($users as $user) {
            $assignedVendors = array_rand($vendors, min(2, count($vendors)));

            foreach ((array) $assignedVendors as $vendorIndex) {
                $userVendorData[] = [
                    'user_id' => $user,
                    'vendor_id' => $vendors[$vendorIndex],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('user_vendor')->insert($userVendorData);
    }
}

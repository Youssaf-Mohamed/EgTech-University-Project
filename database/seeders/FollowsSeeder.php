<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;

class FollowsSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $vendors = Vendor::all();

        if ($users->isEmpty() || $vendors->isEmpty()) {
            $this->command->info('No users or vendors found. Skipping follows seeding.');
            return;
        }

        foreach ($users as $user) {
            $randomVendors = $vendors->random(rand(1, 3));

            foreach ($randomVendors as $vendor) {
                $user->followedVendors()->syncWithoutDetaching([$vendor->id]);
            }
        }
    }
}

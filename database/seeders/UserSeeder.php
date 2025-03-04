<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'gender' => 'male',
                'is_active' => true,
                'role' => 'admin',
            ],
            [
                'name' => 'Vendor User',
                'email' => 'vendor@example.com',
                'password' => Hash::make('password'),
                'phone' => '0987654321',
                'gender' => 'female',
                'is_active' => true,
                'role' => 'vendor',
            ],
        ];

        // Generate 10 regular users
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'phone' => '555000' . $i,
                'gender' => array_rand(['male' => 'male', 'female' => 'female', 'other' => 'other']),
                'is_active' => true,
                'role' => 'user',
            ];
        }

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Assign profile pictures based on gender
            $placeholderImage = match ($user->gender) {
                'male' => 'images/male-placeholder.png',
                'female' => 'images/female-placeholder.png',
                default => 'images/other-placeholder.png',
            };

            // Store the image in the media library
            $user->addMedia(public_path($placeholderImage))
                ->preservingOriginal()
                ->toMediaCollection('profile_pictures');
        }
    }
}

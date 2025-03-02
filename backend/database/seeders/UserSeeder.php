<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'phone' => '0123456789',
                'address' => '123 Admin Street',
                'gender' => 'male',
                'is_active' => true,
                'role' => 'admin',
            ],
            [
                'name' => 'Vendor User',
                'email' => 'vendor@example.com',
                'password' => Hash::make('password'),
                'phone' => '0987654321',
                'address' => '456 Vendor Avenue',
                'gender' => 'female',
                'is_active' => true,
                'role' => 'vendor',
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'phone' => '01122334455',
                'address' => '789 User Road',
                'gender' => 'other',
                'is_active' => false,
                'role' => 'user',
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

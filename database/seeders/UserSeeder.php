<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🧍‍♂️ Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('11111111'),
            'phone' => '01700000000',
            'address' => 'Dhaka, Bangladesh',
            'role' => 'admin',
            'status' => 1,
        ]);

        // 🧍‍♂️ Merchant user
        User::create([
            'name' => 'Merchant One',
            'email' => 'merchant1@gmail.com',
            'password' => Hash::make('11111111'),
            'phone' => '01811111111',
            'address' => 'Chittagong, Bangladesh',
            'role' => 'merchant',
            'status' => 1,
        ]);

        // 🧍‍♂️ Merchant user
        User::create([
            'name' => 'Merchant Two',
            'email' => 'merchant2@gmail.com',
            'password' => Hash::make('11111111'),
            'phone' => '01811111111',
            'address' => 'Chittagong, Bangladesh',
            'role' => 'merchant',
            'status' => 0,
        ]);

        // 🧍‍♂️ Merchant user
        User::create([
            'name' => 'Merchant Three',
            'email' => 'merchant3@gmail.com',
            'password' => Hash::make('11111111'),
            'phone' => '01811111111',
            'address' => 'Chittagong, Bangladesh',
            'role' => 'merchant',
            'status' => 0,
        ]);

        // 🧍‍♂️ Booking Operator user
        User::create([
            'name' => 'Booking Operator',
            'email' => 'operator@gmail.com',
            'password' => Hash::make('11111111'),
            'phone' => '01922222222',
            'address' => 'Sylhet, Bangladesh',
            'role' => 'booking operator',
            'user_id' => 2, // belongs to Merchant One
            'status' => 1,
        ]);
    }
}

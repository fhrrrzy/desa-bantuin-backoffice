<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@desa-bantuin.com',
            'phone_number' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'avatar_url' => null,
        ]);

        // Create Warga User
        User::create([
            'name' => 'Warga Test',
            'email' => 'warga@desa-bantuin.com',
            'phone_number' => '081234567891',
            'password' => Hash::make('warga123'),
            'role' => 'warga',
            'avatar_url' => null,
        ]);

        // Create additional warga users for testing
        User::create([
            'name' => 'Ahmad Supriadi',
            'email' => 'ahmad@example.com',
            'phone_number' => '081234567892',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'avatar_url' => null,
        ]);

        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'phone_number' => '081234567893',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'avatar_url' => null,
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone_number' => '081234567894',
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'avatar_url' => null,
        ]);
    }
}

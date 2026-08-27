<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat akun Admin default
        User::create([
            'name' => 'Admin FTI',
            'email' => 'admin@triathlon.test',
            'password' => Hash::make('admin123'),
        ]);
    }
}

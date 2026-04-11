<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@mrf.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@mrf.com',
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
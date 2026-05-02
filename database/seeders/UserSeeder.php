<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // الحرفي (صاحب المنتجات)
        User::create([
            "name" => "Artisan User",
            "email" => "artisan@example.com",
            "phone" => "123456789",
            "address" => "Damascus, Syria",
            "password" => Hash::make("password"),
            "role" => "artisan"
        ]);

        // مستخدمين عاديين (للتجربة)
        for ($i = 1; $i <= 25; $i++) {
            User::create([
                "name" => "User $i",
                "email" => "user$i@example.com",
                "password" => Hash::make("password"),
                "role" => "user"
            ]);
        }
    }
}
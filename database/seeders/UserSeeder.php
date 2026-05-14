<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // الحرفيون (Artisans)
        $artisans = [
            [
                "name" => "Wool & Warmth",
                "email" => "wool@warmth.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "artisan",
                "bio" => "🧶 Handmade knitting & crochet | Warm and cozy pieces",
                "profile_image" => "images/avatar3.jpg",
            ],
            [
                "name" => "Sarah Arts",
                "email" => "sarah@example.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "artisan",
                "bio" => "🖌️ Mixed media artist | Painting & Drawing",
                "profile_image" => "images/avatar1.jpg",
            ],
            [
                "name" => "Luxury Candles",
                "email" => "info@luxurycandles.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "artisan",
                "bio" => "🕯️ Hand-poured luxury candles | Natural soy wax",
                "profile_image" => "images/avatar2.jpg",
            ],
            [
                "name" => "Glass Art",
                "email" => "glass@art.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "artisan",
                "bio" => "🔮 Stained glass & mosaic art | Unique handmade pieces",
                "profile_image" => "images/avatar4.jpg",
            ],
            [
                "name" => "Sara's Scents",
                "email" => "sara@scents.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "artisan",
                "bio" => "🌹 Natural perfumes & oils | Amber & Oud",
                "profile_image" => "images/avatar5.jpg",
            ],
        ];

        foreach ($artisans as $artisan) {
            User::create($artisan);
        }

        // المستخدمون العاديون
        $users = [
            [
                "name" => "Rawan Art",
                "email" => "rawan@example.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "user",
                "bio" => "🎨 Ceramics artist\nCreating unique handmade pottery ✨",
                "profile_image" => "images/avatar2.jpg",
            ],
            [
                "name" => "Nour Design",
                "email" => "nour@example.com",
                "phone" => "123456789",
                "address" => "Amman, Jordan",
                "password" => Hash::make("password"),
                "role" => "user",
                "bio" => "✨ Art enthusiast | Home decor lover",
                "profile_image" => null,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // مستخدمين إضافيين (للتجربة)
        for ($i = 8; $i <= 30; $i++) {
            User::create([
                "name" => "User $i",
                "email" => "user$i@gmail.com",
                "password" => Hash::make("password"),
                "role" => "user",
                "bio" => null,
                "profile_image" => null,
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\User;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        // جلب الحرفيين
        $artisans = User::where('role', 'artisan')->take(5)->get();
        
        if ($artisans->isEmpty()) {
            $artisans = User::factory(5)->create(['role' => 'artisan']);
        }
        
        $stories = [
            'Wool & Warmth',
            'Sarah Arts',
            'Luxury Candles',
            'Glass Art',
            "Sara's Scents"
        ];
        
        foreach ($artisans as $index => $artisan) {
            Story::create([
                'user_id' => $artisan->id,
                'image' => 'stories/story_' . ($index + 1) . '.jpg',
                'caption' => $stories[$index] ?? 'New Story',
                'expires_at' => now()->addHours(24),
                'is_active' => true,
                'created_at' => now(),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $admin = User::where('role', 'admin')->first();
        
        $posts = [
            // منشورات Rawan Art (user_id:6)
            [
                'user_id' => 6,
                'content' => 'Just finished this beautiful ceramic vase! 🏺✨ Handmade with love and patience. What do you think?',
                'occasion' => 'handmade_gifts',
                'likes_count' => 124,
                'comments_count' => 18,
            ],
            [
                'user_id' => 6,
                'content' => 'My new art studio setup is finally complete! So excited to create more pieces here. 🎨',
                'occasion' => 'handmade_gifts',
                'likes_count' => 89,
                'comments_count' => 12,
            ],
            [
                'user_id' => 6,
                'content' => 'Love these handmade ceramics! 🏺 So inspiring!',
                'occasion' => 'handmade_gifts',
                'likes_count' => 45,
                'comments_count' => 7,
            ],
            // منشورات Sarah Arts (user_id:2)
            [
                'user_id' => 2,
                'content' => 'Just finished this mixed media painting! 🎨 What do you think?',
                'occasion' => 'handmade_gifts',
                'likes_count' => 67,
                'comments_count' => 9,
            ],
            [
                'user_id' => 2,
                'content' => 'Looking for unique gift ideas for Mother\'s Day? Check out my latest collection! 💝',
                'occasion' => 'mothers_day',
                'likes_count' => 112,
                'comments_count' => 23,
            ],
            // منشورات Luxury Candles (user_id:3)
            [
                'user_id' => 3,
                'content' => 'New lavender vanilla candles just dropped! 🕯️ Perfect for relaxing evenings.',
                'occasion' => 'handmade_gifts',
                'likes_count' => 203,
                'comments_count' => 31,
            ],
            // منشورات Glass Art (user_id:4)
            [
                'user_id' => 4,
                'content' => 'Hand-blown glass vase inspired by the ocean 🌊💙 Each piece is unique!',
                'occasion' => 'wedding',
                'likes_count' => 156,
                'comments_count' => 19,
            ],
        ];

        foreach ($posts as $index => $postData) {
            Post::create([
                'user_id' => $postData['user_id'],
                'content' => $postData['content'],
                'occasion' => $postData['occasion'],
                'likes_count' => $postData['likes_count'],
                'comments_count' => $postData['comments_count'],
                'created_at' => now()->subDays($index),
                'updated_at' => now()->subDays($index),
            ]);
        }

        $this->command->info('✅ Posts seeded successfully!');
    }
}
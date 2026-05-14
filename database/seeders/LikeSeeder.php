<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Like;
use App\Models\Product;
use App\Models\Post;
use Carbon\Carbon;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        // ========== إعجابات على المنتجات ==========
        $productLikes = [
            // منتج 1 (Amber Perfume) - إعجابات
            ['user_id' => 2, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 3, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 4, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 5, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(4)],
            ['user_id' => 6, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(5)],
            ['user_id' => 7, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(6)],
            ['user_id' => 8, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(7)],
            ['user_id' => 9, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(8)],
            ['user_id' => 10, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(9)],
            ['user_id' => 11, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(10)],
            ['user_id' => 12, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(11)],
            ['user_id' => 13, 'product_id' => 1, 'created_at' => Carbon::now()->subDays(12)],
            
            // منتج 2 (Luxury Candle)
            ['user_id' => 2, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 3, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 4, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 5, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(4)],
            ['user_id' => 6, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(5)],
            ['user_id' => 7, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(6)],
            ['user_id' => 8, 'product_id' => 2, 'created_at' => Carbon::now()->subDays(7)],
            
            // منتج 3 (Woolen Scarf)
            ['user_id' => 2, 'product_id' => 3, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 3, 'product_id' => 3, 'created_at' => Carbon::now()->subDays(4)],
            ['user_id' => 4, 'product_id' => 3, 'created_at' => Carbon::now()->subDays(6)],
            ['user_id' => 5, 'product_id' => 3, 'created_at' => Carbon::now()->subDays(8)],
            ['user_id' => 6, 'product_id' => 3, 'created_at' => Carbon::now()->subDays(10)],
            
            // منتج 4 (Classic Watch)
            ['user_id' => 2, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 3, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 4, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(5)],
            ['user_id' => 5, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(7)],
            ['user_id' => 6, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(9)],
            ['user_id' => 7, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(11)],
            ['user_id' => 8, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(13)],
            ['user_id' => 9, 'product_id' => 4, 'created_at' => Carbon::now()->subDays(15)],
        ];

        foreach ($productLikes as $like) {
            Like::create([
                'user_id' => $like['user_id'],
                'likeable_type' => 'App\\Models\\Product',
                'likeable_id' => $like['product_id'],
                'created_at' => $like['created_at'],
                'updated_at' => $like['created_at'],
            ]);
        }

        // تحديث عدد الإعجابات في المنتجات
        for ($i = 1; $i <= 4; $i++) {
            $count = Like::where('likeable_type', 'App\\Models\\Product')
                ->where('likeable_id', $i)
                ->count();
            \App\Models\Product::where('id', $i)->update(['likes_count' => $count]);
        }

        // ========== إعجابات على المنشورات ==========
        $postLikes = [
            // منشور 1
            ['user_id' => 2, 'post_id' => 1, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 3, 'post_id' => 1, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 4, 'post_id' => 1, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 5, 'post_id' => 1, 'created_at' => Carbon::now()->subDays(4)],
            ['user_id' => 6, 'post_id' => 1, 'created_at' => Carbon::now()->subDays(5)],
            
            // منشور 2
            ['user_id' => 2, 'post_id' => 2, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 3, 'post_id' => 2, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 4, 'post_id' => 2, 'created_at' => Carbon::now()->subDays(4)],
            
            // منشور 3
            ['user_id' => 2, 'post_id' => 3, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 3, 'post_id' => 3, 'created_at' => Carbon::now()->subDays(4)],
            ['user_id' => 4, 'post_id' => 3, 'created_at' => Carbon::now()->subDays(5)],
            
            // منشور 6 (منشور مشهور)
            ['user_id' => 2, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 3, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 4, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 5, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 7, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 8, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 9, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 10, 'post_id' => 6, 'created_at' => Carbon::now()->subDays(1)],
        ];

        foreach ($postLikes as $like) {
            Like::create([
                'user_id' => $like['user_id'],
                'likeable_type' => 'App\\Models\\Post',
                'likeable_id' => $like['post_id'],
                'created_at' => $like['created_at'],
                'updated_at' => $like['created_at'],
            ]);
        }

        // تحديث عدد الإعجابات في المنشورات
        $posts = \App\Models\Post::all();
        foreach ($posts as $post) {
            $count = Like::where('likeable_type', 'App\\Models\\Post')
                ->where('likeable_id', $post->id)
                ->count();
            $post->update(['likes_count' => $count]);
        }

        $this->command->info('✅ Likes seeded successfully!');
    }
}
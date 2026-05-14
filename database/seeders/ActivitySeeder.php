<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            // ========== متابعات (Follow) ==========
            ['user_id' => 6, 'actor_id' => 2, 'type' => 'follow', 'target_type' => 'User', 'target_id' => 6, 'target_title' => 'Sarah Arts', 'created_at' => Carbon::now()->subHours(2)],
            ['user_id' => 6, 'actor_id' => 3, 'type' => 'follow', 'target_type' => 'User', 'target_id' => 6, 'target_title' => 'Luxury Candles', 'created_at' => Carbon::now()->subHours(5)],
            ['user_id' => 6, 'actor_id' => 4, 'type' => 'follow', 'target_type' => 'User', 'target_id' => 6, 'target_title' => 'Glass Art', 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'actor_id' => 5, 'type' => 'follow', 'target_type' => 'User', 'target_id' => 6, 'target_title' => "Sara's Scents", 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 6, 'actor_id' => 7, 'type' => 'follow', 'target_type' => 'User', 'target_id' => 6, 'target_title' => 'Nour Design', 'created_at' => Carbon::now()->subDays(3)],
            
            // ========== إعجابات على منشورات (Like Post) ==========
            ['user_id' => 6, 'actor_id' => 2, 'type' => 'like_post', 'target_type' => 'Post', 'target_id' => 1, 'target_title' => 'Beautiful ceramic vase!', 'created_at' => Carbon::now()->subHours(3)],
            ['user_id' => 6, 'actor_id' => 3, 'type' => 'like_post', 'target_type' => 'Post', 'target_id' => 1, 'target_title' => 'Beautiful ceramic vase!', 'created_at' => Carbon::now()->subHours(4)],
            ['user_id' => 6, 'actor_id' => 4, 'type' => 'like_post', 'target_type' => 'Post', 'target_id' => 2, 'target_title' => 'New art studio setup', 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'actor_id' => 5, 'type' => 'like_post', 'target_type' => 'Post', 'target_id' => 3, 'target_title' => 'Love these handmade ceramics!', 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'actor_id' => 7, 'type' => 'like_post', 'target_type' => 'Post', 'target_id' => 6, 'target_title' => 'New lavender vanilla candles!', 'created_at' => Carbon::now()->subHours(1)],
            
            // ========== تعليقات على منشورات (Comment Post) ==========
            ['user_id' => 6, 'actor_id' => 2, 'type' => 'comment_post', 'target_type' => 'Post', 'target_id' => 1, 'target_title' => 'This is so beautiful!', 'created_at' => Carbon::now()->subHours(6)],
            ['user_id' => 6, 'actor_id' => 3, 'type' => 'comment_post', 'target_type' => 'Post', 'target_id' => 6, 'target_title' => 'Where can I buy these?', 'created_at' => Carbon::now()->subHours(8)],
            ['user_id' => 6, 'actor_id' => 4, 'type' => 'comment_post', 'target_type' => 'Post', 'target_id' => 8, 'target_title' => 'This is stunning!', 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'actor_id' => 5, 'type' => 'comment_post', 'target_type' => 'Post', 'target_id' => 1, 'target_title' => 'How long did it take?', 'created_at' => Carbon::now()->subDays(2)],
            
            // ========== حفظ منشورات (Save Post) ==========
            ['user_id' => 6, 'actor_id' => 2, 'type' => 'save_post', 'target_type' => 'Post', 'target_id' => 4, 'target_title' => 'Mixed media painting', 'created_at' => Carbon::now()->subHours(12)],
            ['user_id' => 6, 'actor_id' => 3, 'type' => 'save_post', 'target_type' => 'Post', 'target_id' => 8, 'target_title' => 'Hand-blown glass vase', 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'actor_id' => 7, 'type' => 'save_post', 'target_type' => 'Post', 'target_id' => 1, 'target_title' => 'Beautiful ceramic vase!', 'created_at' => Carbon::now()->subDays(2)],
            
            // ========== حفظ منتجات (Save Product) ==========
            ['user_id' => 6, 'actor_id' => 2, 'type' => 'save_product', 'target_type' => 'Product', 'target_id' => 1, 'target_title' => 'Amber Perfume', 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'actor_id' => 3, 'type' => 'save_product', 'target_type' => 'Product', 'target_id' => 2, 'target_title' => 'Luxury Candle', 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 6, 'actor_id' => 4, 'type' => 'save_product', 'target_type' => 'Product', 'target_id' => 4, 'target_title' => 'Classic Watch', 'created_at' => Carbon::now()->subDays(3)],
            
            // ========== بعض الأنشطة كمقروءة ==========
            ['user_id' => 6, 'actor_id' => 2, 'type' => 'like_post', 'target_type' => 'Post', 'target_id' => 1, 'target_title' => 'Beautiful ceramic vase!', 'read_at' => Carbon::now()->subHours(2), 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 6, 'actor_id' => 3, 'type' => 'follow', 'target_type' => 'User', 'target_id' => 6, 'target_title' => 'Luxury Candles', 'read_at' => Carbon::now()->subDays(1), 'created_at' => Carbon::now()->subDays(4)],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }

        $this->command->info('✅ Activities seeded successfully!');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // المجموعة 1: المستخدمون (الأساس)
        $this->call(UserSeeder::class);
        
        // المجموعة 2: المحتوى الأساسي
        $this->call([
            ProductSeeder::class,
            OfferSeeder::class,
            ProductDetailSeeder::class,
        ]);
        
        // المجموعة 3: المنشورات والتعليقات
        $this->call([
            PostSeeder::class,
            CommentSeeder::class,
        ]);
        
        // المجموعة 4: الإعجابات والحفظ والمتابعات
        $this->call([
            LikeSeeder::class,
            SaveSeeder::class,
            FollowerSeeder::class,
        ]);
        
        // المجموعة 5: القصص والطلبات
        $this->call([
            StorySeeder::class,
            OrdersTableSeeder::class,
            OrderItemDetailsTableSeeder::class,
        ]);
        
        // المجموعة 6: المعارض والأنشطة
        $this->call([
            ExhibitionSeeder::class,
            ExhibitionRegistrationSeeder::class,  // ✅ جديد
        ]);
        
        // المجموعة 7: نشاطات ومشاهدات القصص
        $this->call([
            ActivitySeeder::class,
            StoryViewSeeder::class,  // ✅ جديد
        ]);
    }
}
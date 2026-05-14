<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Save;
use Carbon\Carbon;

class SaveSeeder extends Seeder
{
    public function run(): void
    {
        $saves = [
            // ========== منتجات محفوظة ==========
            // مستخدم 6 (Rawan) حفظ منتجات
            ['user_id' => 6, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 1, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 6, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 2, 'created_at' => Carbon::now()->subDays(3)],
            ['user_id' => 6, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 4, 'created_at' => Carbon::now()->subDays(5)],
            
            // مستخدم 7 (Nour) حفظ منتجات
            ['user_id' => 7, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 1, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 7, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 3, 'created_at' => Carbon::now()->subDays(4)],
            
            // مستخدم 2 (Sarah) حفظ منتجات
            ['user_id' => 2, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 2, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 2, 'saveable_type' => 'App\\Models\\Product', 'saveable_id' => 4, 'created_at' => Carbon::now()->subDays(2)],
            
            // ========== منشورات محفوظة ==========
            // مستخدم 6 (Rawan) حفظ منشورات
            ['user_id' => 6, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 4, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 6, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 6, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 6, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 8, 'created_at' => Carbon::now()->subDays(3)],
            
            // مستخدم 7 (Nour) حفظ منشورات
            ['user_id' => 7, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 1, 'created_at' => Carbon::now()->subDays(1)],
            ['user_id' => 7, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 2, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 7, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 6, 'created_at' => Carbon::now()->subDays(3)],
            
            // مستخدم 3 (Luxury Candles) حفظ منشورات
            ['user_id' => 3, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 1, 'created_at' => Carbon::now()->subDays(2)],
            ['user_id' => 3, 'saveable_type' => 'App\\Models\\Post', 'saveable_id' => 8, 'created_at' => Carbon::now()->subDays(4)],
        ];

        foreach ($saves as $save) {
            Save::create($save);
        }

        $this->command->info('✅ Saves seeded successfully!');
    }
}
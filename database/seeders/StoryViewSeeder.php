<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\User;
use App\Models\StoryView;
use Carbon\Carbon;

class StoryViewSeeder extends Seeder
{
    public function run(): void
    {
        // جلب القصص والمستخدمين
        $stories = Story::all();
        $users = User::where('role', 'user')->take(15)->get(); // أول 15 مستخدم عادي
        
        if ($stories->isEmpty()) {
            $this->command->warn('⚠️ No stories found! Run StorySeeder first.');
            return;
        }

        $viewCount = 0;

        foreach ($stories as $story) {
            // عدد مشاهدات عشوائي للقصة (2-7 مشاهدات)
            $viewsPerStory = rand(2, 7);
            
            // خلط المستخدمين عشان نختار عشوائياً
            $randomUsers = $users->shuffle();
            
            for ($i = 0; $i < $viewsPerStory && $i < $randomUsers->count(); $i++) {
                $viewer = $randomUsers[$i];
                
                // لا نضيف مشاهدة إذا كان صاحب القصة هو نفسه المشاهد
                if ($story->user_id == $viewer->id) {
                    continue;
                }
                
                // التحقق من عدم وجود مشاهدة مكررة
                $exists = StoryView::where('story_id', $story->id)
                    ->where('viewer_id', $viewer->id)
                    ->exists();
                
                if (!$exists) {
                    StoryView::create([
                        'story_id' => $story->id,
                        'viewer_id' => $viewer->id,
                        'viewed_at' => Carbon::now()->subHours(rand(1, 48)),
                        'created_at' => Carbon::now()->subHours(rand(1, 48)),
                        'updated_at' => Carbon::now()->subHours(rand(1, 48)),
                    ]);
                    
                    $viewCount++;
                }
            }
        }

        $this->command->info("✅ {$viewCount} story views seeded successfully!");
    }
}
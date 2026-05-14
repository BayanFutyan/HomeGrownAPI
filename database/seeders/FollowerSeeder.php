<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class FollowerSeeder extends Seeder
{
    public function run(): void
    {
        $follows = [
            // متابعو Rawan Art (id:6)
            ['follower_id' => 2, 'following_id' => 6, 'rating' => 5, 'created_at' => Carbon::now()->subDays(1)],
            ['follower_id' => 3, 'following_id' => 6, 'rating' => 4, 'created_at' => Carbon::now()->subDays(2)],
            ['follower_id' => 4, 'following_id' => 6, 'rating' => null, 'created_at' => Carbon::now()->subDays(3)],
            ['follower_id' => 5, 'following_id' => 6, 'rating' => 5, 'created_at' => Carbon::now()->subDays(4)],
            ['follower_id' => 7, 'following_id' => 6, 'rating' => null, 'created_at' => Carbon::now()->subDays(5)],
            ['follower_id' => 8, 'following_id' => 6, 'rating' => 3, 'created_at' => Carbon::now()->subDays(6)],
            ['follower_id' => 9, 'following_id' => 6, 'rating' => null, 'created_at' => Carbon::now()->subDays(7)],
            ['follower_id' => 10, 'following_id' => 6, 'rating' => 4, 'created_at' => Carbon::now()->subDays(8)],
            
            // متابعو Sarah Arts (id:2)
            ['follower_id' => 6, 'following_id' => 2, 'rating' => null, 'created_at' => Carbon::now()->subDays(2)],
            ['follower_id' => 3, 'following_id' => 2, 'rating' => 5, 'created_at' => Carbon::now()->subDays(3)],
            ['follower_id' => 4, 'following_id' => 2, 'rating' => null, 'created_at' => Carbon::now()->subDays(4)],
            ['follower_id' => 7, 'following_id' => 2, 'rating' => 3, 'created_at' => Carbon::now()->subDays(5)],
            
            // متابعو Luxury Candles (id:3)
            ['follower_id' => 2, 'following_id' => 3, 'rating' => 3, 'created_at' => Carbon::now()->subDays(1)],
            ['follower_id' => 6, 'following_id' => 3, 'rating' => null, 'created_at' => Carbon::now()->subDays(2)],
            ['follower_id' => 7, 'following_id' => 3, 'rating' => 4, 'created_at' => Carbon::now()->subDays(3)],
            ['follower_id' => 8, 'following_id' => 3, 'rating' => null, 'created_at' => Carbon::now()->subDays(4)],
            
            // متابعو Glass Art (id:4)
            ['follower_id' => 2, 'following_id' => 4, 'rating' => null, 'created_at' => Carbon::now()->subDays(2)],
            ['follower_id' => 3, 'following_id' => 4, 'rating' => 5, 'created_at' => Carbon::now()->subDays(3)],
            ['follower_id' => 6, 'following_id' => 4, 'rating' => null, 'created_at' => Carbon::now()->subDays(4)],
            ['follower_id' => 7, 'following_id' => 4, 'rating' => 4, 'created_at' => Carbon::now()->subDays(5)],
            
            // متابعو Sara's Scents (id:5)
            ['follower_id' => 6, 'following_id' => 5, 'rating' => null, 'created_at' => Carbon::now()->subDays(3)],
            ['follower_id' => 2, 'following_id' => 5, 'rating' => 4, 'created_at' => Carbon::now()->subDays(4)],
            
            // متابعو Nour Design (id:7)
            ['follower_id' => 6, 'following_id' => 7, 'rating' => null, 'created_at' => Carbon::now()->subDays(5)],
            ['follower_id' => 2, 'following_id' => 7, 'rating' => 3, 'created_at' => Carbon::now()->subDays(6)],
        ];

        foreach ($follows as $follow) {
            $user = User::find($follow['follower_id']);
            if ($user) {
                $user->following()->attach($follow['following_id'], [
                    'rating' => $follow['rating'],
                    'created_at' => $follow['created_at'],
                    'updated_at' => $follow['created_at'],
                ]);
            }
        }

        $this->command->info('✅ Followers seeded successfully!');
    }
}
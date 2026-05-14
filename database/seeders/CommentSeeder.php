<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            // ========== Product 1 comments ==========
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 2,
                'comment' => 'Nice scent but took a bit longer to deliver.',
                'likes_count' => 4,
                'created_at' => Carbon::now()->subDays(1)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 3,
                'comment' => 'Lovely fragrance! Perfect for special occasions.',
                'likes_count' => 7,
                'created_at' => Carbon::now()->subDays(2)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 4,
                'comment' => 'Absolutely in love with this perfume!',
                'likes_count' => 11,
                'created_at' => Carbon::now()->subDays(4)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 5,
                'comment' => 'The packaging was beautiful and the smell is amazing.',
                'likes_count' => 5,
                'created_at' => Carbon::now()->subDays(5)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 6,
                'comment' => 'Very elegant perfume. I would buy it again.',
                'likes_count' => 3,
                'created_at' => Carbon::now()->subDays(7)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 7,
                'comment' => 'Soft fragrance and really suitable as a gift.',
                'likes_count' => 6,
                'created_at' => Carbon::now()->subDays(8)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 8,
                'comment' => 'Good quality, but a bit expensive.',
                'likes_count' => 2,
                'created_at' => Carbon::now()->subDays(14)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 9,
                'comment' => 'I got many compliments when wearing this!',
                'likes_count' => 8,
                'created_at' => Carbon::now()->subDays(15)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 10,
                'comment' => 'Worth every penny!',
                'likes_count' => 9,
                'created_at' => Carbon::now()->subDays(21)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 11,
                'comment' => 'The scent lasts all day long.',
                'likes_count' => 10,
                'created_at' => Carbon::now()->subDays(22)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 12,
                'comment' => 'My favorite perfume!',
                'likes_count' => 12,
                'created_at' => Carbon::now()->subDays(30)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 1,
                'user_id' => 13,
                'comment' => 'Excellent product, highly recommended.',
                'likes_count' => 6,
                'created_at' => Carbon::now()->subDays(32)
            ],

            // ========== Product 2 comments ==========
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 2,
                'user_id' => 14,
                'comment' => 'Makes my room smell amazing!',
                'likes_count' => 5,
                'created_at' => Carbon::now()->subDays(3)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 2,
                'user_id' => 15,
                'comment' => 'Very relaxing scent.',
                'likes_count' => 3,
                'created_at' => Carbon::now()->subDays(7)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 2,
                'user_id' => 16,
                'comment' => 'Great quality candle.',
                'likes_count' => 4,
                'created_at' => Carbon::now()->subDays(14)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 2,
                'user_id' => 17,
                'comment' => 'Burns evenly and smells great.',
                'likes_count' => 6,
                'created_at' => Carbon::now()->subDays(16)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 2,
                'user_id' => 18,
                'comment' => 'Perfect for gift giving.',
                'likes_count' => 2,
                'created_at' => Carbon::now()->subDays(21)
            ],

            // ========== Product 4 comments ==========
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 19,
                'comment' => 'Beautiful watch, very elegant.',
                'likes_count' => 7,
                'created_at' => Carbon::now()->subDays(2)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 20,
                'comment' => 'Great quality and fast delivery.',
                'likes_count' => 5,
                'created_at' => Carbon::now()->subDays(5)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 21,
                'comment' => 'Looks exactly like the picture.',
                'likes_count' => 4,
                'created_at' => Carbon::now()->subDays(7)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 22,
                'comment' => 'Very comfortable to wear.',
                'likes_count' => 3,
                'created_at' => Carbon::now()->subDays(8)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 23,
                'comment' => 'Excellent value for money.',
                'likes_count' => 6,
                'created_at' => Carbon::now()->subDays(14)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 24,
                'comment' => 'I love this watch!',
                'likes_count' => 8,
                'created_at' => Carbon::now()->subDays(15)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 25,
                'comment' => 'Highly recommend.',
                'likes_count' => 5,
                'created_at' => Carbon::now()->subDays(21)
            ],
            [
                'commentable_type' => 'App\\Models\\Product',
                'commentable_id' => 4,
                'user_id' => 26,
                'comment' => 'Timeless design.',
                'likes_count' => 4,
                'created_at' => Carbon::now()->subDays(30)
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }
    }
}
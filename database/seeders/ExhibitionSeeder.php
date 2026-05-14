<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use Carbon\Carbon;

class ExhibitionSeeder extends Seeder
{
    public function run(): void
    {
        $exhibitions = [
            [
                'owner_id' => 1,
                'title' => 'Spring Handmade Fair',
                'description' => 'A celebration of handmade creativity and local artisans.',
                'image' => 'images/exhibition1.png',
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(8),
                'status' => 'upcoming',
                'type' => 'public',
                'location' => 'Amman, Jordan',
                'participants_count' => 0,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'owner_id' => 1,
                'title' => 'Summer Craft Market',
                'description' => 'Showcasing unique handmade products and talented makers.',
                'image' => 'images/exhibition2.png',
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(5),
                'status' => 'active',
                'type' => 'public',
                'location' => 'Irbid, Jordan',
                'participants_count' => 12,
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'owner_id' => 1,
                'title' => 'Amman Art Week',
                'description' => 'A creative gathering of artists, craftsmen and art lovers.',
                'image' => 'images/exhibition3.png',
                'start_date' => Carbon::now()->addDays(10),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'upcoming',
                'type' => 'public',
                'location' => 'Amman, Jordan',
                'participants_count' => 0,
                'created_at' => Carbon::now()->subDays(20),
            ],
            [
                'owner_id' => 2,
                'title' => 'Winter Makers Showcase',
                'description' => 'An exclusive showcase for selected artisans and designers.',
                'image' => 'images/exhibition4.png',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->subDays(28),
                'status' => 'ended',
                'type' => 'private',
                'location' => 'Amman, Jordan',
                'participants_count' => 25,
                'created_at' => Carbon::now()->subDays(40),
            ],
            [
                'owner_id' => 2,
                'title' => 'New Year Artisan Market',
                'description' => 'A special market to welcome the new year with handmade treasures.',
                'image' => 'images/exhibition5.png',
                'start_date' => Carbon::now()->addDays(20),
                'end_date' => Carbon::now()->addDays(22),
                'status' => 'upcoming',
                'type' => 'private',
                'location' => 'Amman, Jordan',
                'participants_count' => 0,
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'owner_id' => 3,
                'title' => 'Ramadan Artisan Bazaar',
                'description' => 'Special Ramadan exhibition featuring traditional crafts.',
                'image' => 'images/exhibition3.png',
                'start_date' => Carbon::now()->addDays(2),
                'end_date' => Carbon::now()->addDays(7),
                'status' => 'upcoming',
                'type' => 'private',
                'location' => 'Amman, Jordan',
                'participants_count' => 8,
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'owner_id' => 4,
                'title' => 'Dubai Handmade Week',
                'description' => 'An exclusive invitation-only exhibition for premium artisans.',
                'image' => 'images/exhibition4.png',
                'start_date' => Carbon::now()->addDays(15),
                'end_date' => Carbon::now()->addDays(20),
                'status' => 'upcoming',
                'type' => 'private',
                'location' => 'Dubai, UAE',
                'participants_count' => 0,
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($exhibitions as $exhibition) {
            Exhibition::create($exhibition);
        }

        $this->command->info('✅ Exhibitions seeded successfully!');
    }
}
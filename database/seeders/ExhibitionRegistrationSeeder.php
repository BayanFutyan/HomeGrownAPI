<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExhibitionRegistration;
use App\Models\Exhibition;
use App\Models\User;
use Carbon\Carbon;

class ExhibitionRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        // جلب المعارض والحرفيين
        $exhibitions = Exhibition::all();
        $artisans = User::where('role', 'artisan')->get();
        
        if ($exhibitions->isEmpty() || $artisans->isEmpty()) {
            $this->command->warn('⚠️ No exhibitions or artisans found!');
            return;
        }

        $registrations = [
            // معرض 1 (Spring Handmade Fair)
            ['exhibition_id' => 1, 'seller_id' => 2, 'status' => 'pending', 'type' => 'request', 'created_at' => Carbon::now()->subDays(3)],
            ['exhibition_id' => 1, 'seller_id' => 3, 'status' => 'accepted', 'type' => 'request', 'created_at' => Carbon::now()->subDays(4)],
            ['exhibition_id' => 1, 'seller_id' => 4, 'status' => 'pending', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(2)],
            ['exhibition_id' => 1, 'seller_id' => 5, 'status' => 'accepted', 'type' => 'request', 'created_at' => Carbon::now()->subDays(5)],
            
            // معرض 2 (Summer Craft Market)
            ['exhibition_id' => 2, 'seller_id' => 2, 'status' => 'accepted', 'type' => 'request', 'created_at' => Carbon::now()->subDays(7)],
            ['exhibition_id' => 2, 'seller_id' => 3, 'status' => 'accepted', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(6)],
            ['exhibition_id' => 2, 'seller_id' => 4, 'status' => 'accepted', 'type' => 'request', 'created_at' => Carbon::now()->subDays(5)],
            ['exhibition_id' => 2, 'seller_id' => 5, 'status' => 'accepted', 'type' => 'request', 'created_at' => Carbon::now()->subDays(4)],
            
            // معرض 3 (Amman Art Week)
            ['exhibition_id' => 3, 'seller_id' => 2, 'status' => 'pending', 'type' => 'request', 'created_at' => Carbon::now()->subDays(1)],
            ['exhibition_id' => 3, 'seller_id' => 3, 'status' => 'pending', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(2)],
            ['exhibition_id' => 3, 'seller_id' => 4, 'status' => 'accepted', 'type' => 'request', 'created_at' => Carbon::now()->subDays(3)],
            ['exhibition_id' => 3, 'seller_id' => 5, 'status' => 'rejected', 'type' => 'request', 'created_at' => Carbon::now()->subDays(4)],
            
            // معرض 4 (Winter Makers Showcase) - معرض منتهي
            ['exhibition_id' => 4, 'seller_id' => 2, 'status' => 'accepted', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(30)],
            ['exhibition_id' => 4, 'seller_id' => 3, 'status' => 'accepted', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(29)],
            ['exhibition_id' => 4, 'seller_id' => 4, 'status' => 'accepted', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(28)],
            
            // معرض 5 (New Year Artisan Market)
            ['exhibition_id' => 5, 'seller_id' => 6, 'status' => 'pending', 'type' => 'request', 'created_at' => Carbon::now()->subDays(1)],
            ['exhibition_id' => 5, 'seller_id' => 7, 'status' => 'pending', 'type' => 'request', 'created_at' => Carbon::now()->subDays(2)],
            
            // معرض 6 (Ramadan Artisan Bazaar)
            ['exhibition_id' => 6, 'seller_id' => 2, 'status' => 'accepted', 'type' => 'invitation', 'created_at' => Carbon::now()->subDays(3)],
            ['exhibition_id' => 6, 'seller_id' => 3, 'status' => 'pending', 'type' => 'request', 'created_at' => Carbon::now()->subDays(2)],
        ];

        foreach ($registrations as $registration) {
            // التأكد من عدم وجود تكرار
            $exists = ExhibitionRegistration::where('exhibition_id', $registration['exhibition_id'])
                ->where('seller_id', $registration['seller_id'])
                ->exists();
            
            if (!$exists) {
                ExhibitionRegistration::create($registration);
            }
        }

        $this->command->info('✅ Exhibition registrations seeded successfully!');
    }
}
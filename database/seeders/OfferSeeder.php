<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;
use Carbon\Carbon;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        Offer::create([
            "product_id" => 1,
            "discount_value" => 20.00,
            "start_date" => Carbon::now()->subDays(10),
            "end_date" => Carbon::now()->addDays(20),
            "discounted_price" => 96.00,
        ]);

        Offer::create([
            "product_id" => 3,
            "discount_value" => 15.00,
            "start_date" => Carbon::now()->subDays(5),
            "end_date" => Carbon::now()->addDays(10),
            "discounted_price" => 127.50,
        ]);
    }
}
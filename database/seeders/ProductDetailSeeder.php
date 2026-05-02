<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductDetail;

class ProductDetailSeeder extends Seeder
{
    public function run(): void
    {
        $details = [
            // Product 1 details
            ["product_id" => 1, "detail_name" => "Bottle Size", "detail_value" => "250 ml"],
            ["product_id" => 1, "detail_name" => "Material", "detail_value" => "Glass"],
            ["product_id" => 1, "detail_name" => "Fragrance Family", "detail_value" => "Amber & Floral"],
            ["product_id" => 1, "detail_name" => "Longevity", "detail_value" => "8-10 hours"],
            
            // Product 2 details
            ["product_id" => 2, "detail_name" => "Weight", "detail_value" => "200g"],
            ["product_id" => 2, "detail_name" => "Burn Time", "detail_value" => "40 hours"],
            ["product_id" => 2, "detail_name" => "Material", "detail_value" => "Soy Wax"],
            
            // Product 3 details
            ["product_id" => 3, "detail_name" => "Material", "detail_value" => "100% Wool"],
            ["product_id" => 3, "detail_name" => "Size", "detail_value" => "180cm x 60cm"],
            ["product_id" => 3, "detail_name" => "Color", "detail_value" => "Beige"],
            
            // Product 4 details
            ["product_id" => 4, "detail_name" => "Case Material", "detail_value" => "Stainless Steel"],
            ["product_id" => 4, "detail_name" => "Water Resistant", "detail_value" => "50m"],
            ["product_id" => 4, "detail_name" => "Movement", "detail_value" => "Automatic"],
            ["product_id" => 4, "detail_name" => "Strap", "detail_value" => "Genuine Leather"],
        ];

        foreach ($details as $detail) {
            ProductDetail::create($detail);
        }
    }
}
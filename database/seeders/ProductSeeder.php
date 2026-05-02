<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                "seller_id" => 1,
                "name" => "Amber Perfume",
                "category" => "Perfume",
                "description" => "Amber Perfume is a rich and warm fragrance blended with jasmine and sandalwood.",
                "price" => 120.00,
                "stock" => 25,
                "image" => "storage/products/img5.jpg",
                "likes_count" => 20,
                "is_sale" => true,
                "sales_count" => 40,
            ],
            [
                "seller_id" => 1,
                "name" => "Luxury Candle",
                "category" => "Candle",
                "description" => "A premium handcrafted candle designed to create a calm atmosphere.",
                "price" => 80.00,
                "stock" => 50,
                "image" => "storage/products/img6.jpg",
                "likes_count" => 90,
                "is_sale" => false,
                "sales_count" => 150,
            ],
            [
                "seller_id" => 1,
                "name" => "Woolen Scarf",
                "category" => "Scarf",
                "description" => "A soft handmade scarf that provides warmth and comfort.",
                "price" => 150.00,
                "stock" => 0,
                "image" => "storage/products/img4.jpg",
                "likes_count" => 135,
                "is_sale" => true,
                "sales_count" => 120,
            ],
            [
                "seller_id" => 1,
                "name" => "Classic Watch",
                "category" => "Watch",
                "description" => "An elegant watch with a minimalist timeless design.",
                "price" => 350.00,
                "stock" => 15,
                "image" => "storage/products/img7.jpg",
                "likes_count" => 220,
                "is_sale" => false,
                "sales_count" => 30,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
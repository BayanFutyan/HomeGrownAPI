<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateExpiredOffers extends Command
{
    protected $signature = 'offers:update-expired';
    protected $description = 'Update is_sale status for products with expired offers';

    public function handle()
    {
        // جلب المنتجات التي عليها عرض ولكن العرض منتهي
        $products = Product::where('is_sale', true)
            ->whereHas('offer', function($q) {
                $q->where('end_date', '<', now());
            })
            ->get();
        
        foreach ($products as $product) {
            $product->update(['is_sale' => false]);
            $this->info("Updated product ID {$product->id}: is_sale = false");
        }
        
        $this->info("Updated {$products->count()} products");
        
        return Command::SUCCESS;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Offer;

class UpdateExpiredOffers extends Command
{
    protected $signature = 'offers:update-expired';
    protected $description = 'Update is_sale status for products with expired offers';

    public function handle()
    {
        $expiredProductIds = Offer::where('end_date', '<', now())
            ->pluck('product_id')
            ->unique();

        Product::whereIn('id', $expiredProductIds)->update([
            'is_sale' => false,
        ]);

        Offer::where('end_date', '<', now())->delete();

        $this->info('Updated expired offers successfully');

        return Command::SUCCESS;
    }
}
///php artisan schedule:work
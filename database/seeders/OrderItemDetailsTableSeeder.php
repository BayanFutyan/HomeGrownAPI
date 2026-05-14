<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\OrderItemDetail;

class OrderItemDetailsTableSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding order_item_details...');

        // جلب كل order_items
        $orderItems = OrderItem::all();

        if ($orderItems->isEmpty()) {
            $this->command->warn('⚠️ No order items found! Run OrdersTableSeeder first.');
            return;
        }

        $details = [
            1 => [ // Product ID 1 (Amber Perfume / أي منتج)
                ['detail_name' => 'Bottle Size', 'detail_value' => '250 ml'],
                ['detail_name' => 'Material', 'detail_value' => 'Glass'],
                ['detail_name' => 'Scent Family', 'detail_value' => 'Amber & Floral'],
            ],
            2 => [ // Product ID 2 (Luxury Candle)
                ['detail_name' => 'Weight', 'detail_value' => '300g'],
                ['detail_name' => 'Burn Time', 'detail_value' => '50 hours'],
                ['detail_name' => 'Scent', 'detail_value' => 'Lavender Vanilla'],
            ],
            3 => [ // Product ID 3 (Woolen Scarf)
                ['detail_name' => 'Material', 'detail_value' => '100% Wool'],
                ['detail_name' => 'Size', 'detail_value' => '180cm x 40cm'],
                ['detail_name' => 'Color', 'detail_value' => 'Cream Beige'],
            ],
            4 => [ // Product ID 4 (Classic Watch)
                ['detail_name' => 'Case Diameter', 'detail_value' => '40mm'],
                ['detail_name' => 'Band Material', 'detail_value' => 'Genuine Leather'],
                ['detail_name' => 'Water Resistance', 'detail_value' => '5 ATM'],
            ],
        ];

        foreach ($orderItems as $orderItem) {
            $productId = $orderItem->product_id;
            
            if (isset($details[$productId])) {
                foreach ($details[$productId] as $detail) {
                    // تجنب التكرار
                    $exists = OrderItemDetail::where('order_item_id', $orderItem->id)
                        ->where('detail_name', $detail['detail_name'])
                        ->exists();
                    
                    if (!$exists) {
                        OrderItemDetail::create([
                            'order_item_id' => $orderItem->id,
                            'detail_name' => $detail['detail_name'],
                            'detail_value' => $detail['detail_value'],
                        ]);
                    }
                }
                
                $this->command->info("✅ Added details for order_item #{$orderItem->id}");
            }
        }

        $this->command->info('🎉 Order item details seeded successfully!');
    }
}
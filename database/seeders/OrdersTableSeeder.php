<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        $sellerId = 1;
        $customers = [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

        foreach ($customers as $index => $customerId) {
            $orderNumber = "02" . (100 + $index);
            $subtotal = 120 + ($index * 10);
            $deliveryFee = 10;
            $discount = 0;
            $total = $subtotal + $deliveryFee - $discount;
            
            $order = Order::create([
                "order_number" => $orderNumber,
                "customer_id" => $customerId,
                "seller_id" => $sellerId,
                "payment_method" => "cash_on_delivery",
                "status" => "pending",
                "subtotal" => $subtotal,
                "delivery_fee" => $deliveryFee,
                "discount" => $discount,
                "total_amount" => $total,
                "note" => "New order #" . $orderNumber,
                "shipping_address" => "Address for customer " . $customerId,
                "delivery_time_slot" => "Tomorrow, 10:00 AM - 2:00 PM",
                "created_at" => now()->subDays($index),
            ]);
            
            $productId = ($index % 4) + 1;
            $quantity = ($index % 5) + 1;
            
            $productPrice = match($productId) {
                1 => 120,
                2 => 80,
                3 => 150,
                4 => 350,
            };
            
            OrderItem::create([
                "order_id" => $order->id,
                "product_id" => $productId,
                "quantity" => $quantity,
                "product_price" => $productPrice,
                "subtotal" => $productPrice * $quantity,
            ]);
            
            $this->command->info("✅ Order #{$orderNumber} created for customer {$customerId}");
        }
    }
}
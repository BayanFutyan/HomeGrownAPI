<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            // ============================================================
            // الأعمدة الأساسية
            // ============================================================
            $table->id();                           // id
            $table->foreignId('product_id')         // product_id (مفتاح أجنبي يربط بجدول products)
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->decimal('discount_value', 5, 2);  // discount_value (نسبة الخصم مثلاً 20.00%)
            $table->date('start_date');             // start_date (تاريخ بداية العرض)
            $table->date('end_date');               // end_date (تاريخ نهاية العرض)
            $table->decimal('discounted_price', 10, 2)->nullable();  // discounted_price (السعر بعد الخصم)
            $table->timestamps();                   // created_at, updated_at

            // ============================================================
            // الفهارس (Indexes) لتحسين الأداء
            // ============================================================
            $table->index('product_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
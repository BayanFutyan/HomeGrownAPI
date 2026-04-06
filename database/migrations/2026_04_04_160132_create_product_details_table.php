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
        Schema::create('product_details', function (Blueprint $table) {
            // ============================================================
            // الأعمدة الأساسية
            // ============================================================
            $table->id();                           // id
            $table->foreignId('product_id')         // product_id (مفتاح أجنبي يربط بجدول products)
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->string('detail_name');          // detail_name (مثال: "Bottle Size", "Material", "Weight")
            $table->string('detail_value');         // detail_value (مثال: "250 ml", "Glass", "200g")
            $table->timestamps();                   // created_at, updated_at

            // ============================================================
            // الفهارس (Indexes) لتحسين الأداء
            // ============================================================
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
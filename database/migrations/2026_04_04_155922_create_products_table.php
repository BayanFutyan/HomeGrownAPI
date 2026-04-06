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
        Schema::create('products', function (Blueprint $table) {
            // ============================================================
            // الأعمدة الأساسية
            // ============================================================
            $table->id();                           // id
            $table->foreignId('seller_id')          // seller_id (مفتاح أجنبي يربط بجدول users)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('name');                  // name
            $table->string('category');              // category
            $table->text('description');             // description
            $table->decimal('price', 10, 2);         // price
            $table->integer('stock')->default(0);    // stock
            $table->string('image')->nullable();     // image
            $table->integer('likes_count')->default(0);   // likes_count
            $table->boolean('is_sale')->default(false);    // is_sale
            $table->integer('sales_count')->default(0);    // sales_count
            $table->softDeletes();                   // deleted_at (soft delete)
            $table->timestamps();                    // created_at, updated_at

            // ============================================================
            // الفهارس (Indexes) لتحسين الأداء
            // ============================================================
            $table->index(['seller_id', 'deleted_at']);
            $table->index('category');
            $table->index('is_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
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
        Schema::create('comments', function (Blueprint $table) {
            // ============================================================
            // الأعمدة الأساسية
            // ============================================================
            $table->id();                           // id
            $table->foreignId('product_id')         // product_id (مفتاح أجنبي يربط بجدول products)
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->foreignId('user_id')            // user_id (مفتاح أجنبي يربط بجدول users)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->text('comment');                // comment
            $table->foreignId('parent_id')          // parent_id (للردود على تعليقات أخرى)
                  ->nullable()
                  ->constrained('comments')
                  ->onDelete('cascade');
            $table->integer('likes_count')->default(0);   // likes_count
            $table->timestamps();                   // created_at, updated_at

            // ============================================================
            // الفهارس (Indexes) لتحسين الأداء
            // ============================================================
            $table->index('product_id');
            $table->index('user_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
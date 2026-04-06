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
        Schema::create('followers', function (Blueprint $table) {
            // ============================================================
            // الأعمدة الأساسية
            // ============================================================
            $table->id();                           // id
            $table->foreignId('follower_id')        // follower_id (المستخدم الذي يتابع)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('following_id')       // following_id (المستخدم الذي يتم متابعته)
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->integer('rating')->nullable();  // rating (تقييم المستخدم من 1 إلى 5)
            $table->timestamps();                   // created_at, updated_at

            // ============================================================
            // القيود (Constraints)
            // ============================================================
            // منع تكرار نفس المتابعة (لا يمكن لمستخدم أن يتابع نفس المستخدم مرتين)
            $table->unique(['follower_id', 'following_id']);

            // ============================================================
            // الفهارس (Indexes) لتحسين الأداء
            // ============================================================
            $table->index('follower_id');
            $table->index('following_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
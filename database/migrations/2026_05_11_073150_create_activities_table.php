<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            
            // المستخدم الذي لديه النشاط (صاحب المحتوى)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // المستخدم الذي قام بالإجراء
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');
            
            // نوع النشاط (follow, like_post, comment_post, save_post, save_product)
            $table->string('type');
            
            // نوع الهدف (Post, Product, User)
            $table->string('target_type');
            
            // معرف الهدف
            $table->unsignedBigInteger('target_id');
            
            // عنوان الهدف (للعرض السريع)
            $table->string('target_title')->nullable();
            
            // هل تمت القراءة؟
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // فهارس
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
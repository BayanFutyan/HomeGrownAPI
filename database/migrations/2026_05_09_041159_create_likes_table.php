<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('likeable_type');
            $table->unsignedBigInteger('likeable_id');
            $table->timestamps();
            
            $table->index(['likeable_type', 'likeable_id']);
            $table->unique(['user_id', 'likeable_type', 'likeable_id'], 'likes_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
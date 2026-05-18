<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('artisan_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->check('rating BETWEEN 1 AND 5');
            $table->timestamps();
            
            $table->unique(['user_id', 'artisan_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
};
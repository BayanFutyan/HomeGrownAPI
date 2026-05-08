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
    Schema::create('exhibition_registrations', function (Blueprint $table) {
        $table->id();

        $table->foreignId('exhibition_id')
              ->constrained('exhibitions')
              ->onDelete('cascade');

        $table->foreignId('seller_id')
              ->constrained('users')
              ->onDelete('cascade');

        $table->enum('status', ['pending', 'accepted', 'rejected'])
              ->default('pending');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibition_registrations');
    }
};

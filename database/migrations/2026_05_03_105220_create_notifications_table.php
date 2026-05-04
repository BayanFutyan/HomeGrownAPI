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
    Schema::create('notifications', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('user_id'); // مين بستقبل الإشعار

        $table->string('title');
        $table->text('body');

        $table->string('type')->nullable(); 
        // مثال: product_like / new_product / exhibition_invite

        $table->json('data')->nullable(); 
        // هون بنحط product_id أو exhibition_id

        $table->boolean('is_read')->default(false);

        $table->timestamps();

        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

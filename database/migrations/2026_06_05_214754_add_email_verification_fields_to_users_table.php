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
    Schema::table('users', function (Blueprint $table) {
        $table->string('email_verification_code')->nullable();
        $table->timestamp('email_verification_expires_at')->nullable();
        $table->timestamp('email_verified_at')->nullable();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'email_verification_code',
            'email_verification_expires_at',
            'email_verified_at',
        ]);
    });
}
};

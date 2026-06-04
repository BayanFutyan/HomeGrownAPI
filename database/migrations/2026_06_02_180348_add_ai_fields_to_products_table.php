<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->integer('ai_score')->default(0);

            $table->integer('positive_comments')->default(0);

            $table->integer('neutral_comments')->default(0);

            $table->integer('negative_comments')->default(0);

            $table->string('sentiment_label')
                  ->default('neutral');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropColumn([
                'ai_score',
                'positive_comments',
                'neutral_comments',
                'negative_comments',
                'sentiment_label',
            ]);
        });
    }
};
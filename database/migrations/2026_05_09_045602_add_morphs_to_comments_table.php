<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. أضف الأعمدة الجديدة (commentable_type, commentable_id) وخلّيها nullable مؤقتاً
        Schema::table('comments', function (Blueprint $table) {
            $table->string('commentable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('commentable_id')->nullable()->after('commentable_type');
            
            // أضف فهارس
            $table->index(['commentable_type', 'commentable_id']);
        });

        // 2. انقل البيانات القديمة: خلي المنتجات تكون commentable_type = 'App\Models\Product'
        DB::table('comments')->whereNotNull('product_id')->update([
            'commentable_type' => 'App\\Models\\Product',
            'commentable_id' => DB::raw('product_id'),
        ]);

        // 3. الآن خلي الأعمدة تصير required (غير nullable)
        Schema::table('comments', function (Blueprint $table) {
            $table->string('commentable_type')->nullable(false)->change();
            $table->unsignedBigInteger('commentable_id')->nullable(false)->change();
            
            // 4. احذف العمود القديم product_id
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // استرجع العمود product_id
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            
            // انقل البيانات لproduct_id
            DB::table('comments')
                ->where('commentable_type', 'App\\Models\\Product')
                ->update(['product_id' => DB::raw('commentable_id')]);
            
            // احذف الأعمدة الجديدة
            $table->dropIndex(['commentable_type', 'commentable_id']);
            $table->dropColumn(['commentable_type', 'commentable_id']);
        });
    }
};
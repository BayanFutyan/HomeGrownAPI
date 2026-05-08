<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // أولاً: تعديل هيكل enum ليشمل القيم الجديدة
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'artisan', 'exhibition_owner', 'user') NOT NULL");
        
        // ثانياً: تحديث البيانات القديمة (تحويل project_owner إلى artisan)
        // لأن project_owner لم يعد موجوداً في الـ enum الجديد
        DB::table('users')
            ->where('role', 'project_owner')
            ->update(['role' => 'artisan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // نرجع إلى القيم القديمة
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'artisan', 'project_owner', 'user') NOT NULL");
    }
};
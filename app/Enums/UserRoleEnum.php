<?php
// app/Enums/UserRoleEnum.php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case ARTISAN = 'artisan';      // صاحب المعرض / الحرفي
    case PROJECT_OWNER = 'project_owner';  // صاحب المشروع
    case USER = 'user';            // مستخدم عادي

    /**
     * الحصول على جميع القيم كمصفوفة
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * الحصول على جميع الأسماء كمصفوفة
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * التحقق إذا كان الدور معيناً
     */
    public static function isValid(string $role): bool
    {
        return in_array($role, self::values());
    }
}
<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case ARTISAN = 'artisan';                  // صاحب الحرفة / صاحب المشروع
    case EXHIBITION_OWNER = 'exhibition_owner'; // صاحب المعرض
    case USER = 'user';                        // مستخدم عادي

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function isValid(string $role): bool
    {
        return in_array($role, self::values());
    }
}
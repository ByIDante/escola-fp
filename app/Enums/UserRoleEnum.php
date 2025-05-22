<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRoleEnum: string
{
    case USER = 'USER';
    case ADMIN = 'ADMIN';
    case GUEST = 'GUEST';
    case TEACHER = 'TEACHER';
    case STUDENT = 'STUDENT';

    public static function label(string $type): string
    {
        return match ($type) {
            UserRoleEnum::USER->value => __('User'),
            UserRoleEnum::ADMIN->value => __('Admin'),
            UserRoleEnum::GUEST->value => __('Guest'),
            UserRoleEnum::TEACHER->value => __('Teacher'),
            UserRoleEnum::STUDENT->value => __('Student'),
        };
    }

    public static function getOptions(): array
    {
        return [
            (UserRoleEnum::USER)->value => self::label((UserRoleEnum::USER)->value),
            (UserRoleEnum::ADMIN)->value => self::label((UserRoleEnum::ADMIN)->value),
            (UserRoleEnum::GUEST)->value => self::label((UserRoleEnum::GUEST)->value),
            (UserRoleEnum::TEACHER)->value => self::label((UserRoleEnum::TEACHER)->value),
            (UserRoleEnum::STUDENT)->value => self::label((UserRoleEnum::STUDENT)->value),
        ];
    }

    public static function getValues(): array
    {
        return [
            self::USER->value,
            self::ADMIN->value,
            self::GUEST->value,
            self::TEACHER->value,
            self::STUDENT->value
        ];
    }
}

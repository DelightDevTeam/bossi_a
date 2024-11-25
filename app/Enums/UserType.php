<?php

namespace App\Enums;

enum UserType: int
{
    case Admin = 10;
    case Agent = 20;
    case SubAgent = 25;
    case Player = 30;
    case SystemWallet = 40;

    public static function usernameLength(UserType $type)
    {
        return match ($type) {
            self::Admin => 1,
            self::Agent => 2,
            self::SubAgent => 3,
            self::Player => 4,
            self::SystemWallet => 5
        };
    }

    public static function childUserType(UserType $type)
    {
        return match ($type) {
            self::Admin => self::Agent,
            self::Agent => self::Player,
            self::SubAgent => self::Player,
            self::Player => self::Player,
            self::SystemWallet => self::SystemWallet
        };
    }
}

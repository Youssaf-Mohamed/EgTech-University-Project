<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
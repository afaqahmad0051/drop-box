<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

namespace App\Enums;

enum EquipmentCondition: string
{
    case Suitable = 'suitable';
    case NotSuitable = 'not_suitable';

    public function label(): string
    {
        return match ($this) {
            self::Suitable => 'Layak',
            self::NotSuitable => 'Tidak Layak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Suitable => 'green',
            self::NotSuitable => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

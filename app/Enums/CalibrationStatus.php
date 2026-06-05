<?php

namespace App\Enums;

enum CalibrationStatus: string
{
    case Calibrated = 'calibrated';
    case Expired = 'expired';
    case Pending = 'pending';
    case NotRequired = 'not_required';

    public function label(): string
    {
        return match ($this) {
            self::Calibrated => 'Ter kalibrasi',
            self::Expired => 'Expired',
            self::Pending => 'Pending',
            self::NotRequired => 'Tidak Diperlukan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Calibrated => 'green',
            self::Expired => 'red',
            self::Pending => 'yellow',
            self::NotRequired => 'gray',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

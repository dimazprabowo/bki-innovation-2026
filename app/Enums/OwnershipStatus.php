<?php

namespace App\Enums;

enum OwnershipStatus: string
{
    case Owned = 'owned';
    case Rented = 'rented';
    case Borrowed = 'borrowed';
    case Leased = 'leased';

    public function label(): string
    {
        return match ($this) {
            self::Owned => 'Milik Sendiri',
            self::Rented => 'Sewa',
            self::Borrowed => 'Pinjam',
            self::Leased => 'Leasing',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Owned => 'blue',
            self::Rented => 'purple',
            self::Borrowed => 'orange',
            self::Leased => 'teal',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

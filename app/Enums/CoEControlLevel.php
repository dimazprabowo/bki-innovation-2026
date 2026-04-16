<?php

namespace App\Enums;

enum CoEControlLevel: string
{
    case None = 'none';
    case Standard = 'standard';
    case Enhanced = 'enhanced';
    case Full = 'full';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Tidak Ada',
            self::Standard => 'Standar',
            self::Enhanced => 'Enhanced',
            self::Full => 'Full Control',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::None => 'Tidak memerlukan kontrol CoE',
            self::Standard => 'Review standar oleh CoE',
            self::Enhanced => 'Monitoring ketat dan approval bertahap',
            self::Full => 'Full oversight, approval di setiap milestone',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::None => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            self::Standard => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::Enhanced => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            self::Full => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}

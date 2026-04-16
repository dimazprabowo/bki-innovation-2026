<?php

namespace App\Enums;

enum RiskLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'green',
            self::Medium => 'yellow',
            self::High => 'red',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Low => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::Medium => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::High => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        };
    }

    public function requiresCoE(): bool
    {
        return $this === self::High;
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

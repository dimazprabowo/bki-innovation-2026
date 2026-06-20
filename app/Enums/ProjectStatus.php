<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case OnProgress = 'on_progress';
    case Completed = 'completed';
    case Closed = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::OnProgress => 'On Progress',
            self::Completed => 'Selesai',
            self::Closed => 'Ditutup',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
            self::Active => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            self::OnProgress => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            self::Completed => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400',
            self::Closed => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        };
    }

    /**
     * A project can only be edited while still a draft.
     */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Closed]);
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

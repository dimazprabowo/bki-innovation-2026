<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case None = 'none';
    case CoEReview = 'coe_review';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Belum Diajukan',
            self::CoEReview => 'Review CoE',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::None => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
            self::CoEReview => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
            self::Approved => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            self::Rejected => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

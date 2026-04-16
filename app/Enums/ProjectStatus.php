<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case CoEReview = 'coe_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Stopped = 'stopped';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::CoEReview => 'CoE Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Stopped => 'Stopped',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
            self::CoEReview => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
            self::Approved => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            self::Rejected => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            self::Stopped => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Rejected]);
    }

    public function isSubmittable(): bool
    {
        return in_array($this, [self::Draft, self::Rejected]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Approved, self::Stopped]);
    }
}

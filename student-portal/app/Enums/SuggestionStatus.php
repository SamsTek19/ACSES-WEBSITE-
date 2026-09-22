<?php

namespace App\Enums;

enum SuggestionStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InReview => 'In review',
            self::Resolved => 'Resolved',
            self::Dismissed => 'Dismissed',
        };
    }

    public function marksHandled(): bool
    {
        return match ($this) {
            self::Resolved, self::Dismissed => true,
            default => false,
        };
    }

    public static function values(): array
    {
        return array_map(static fn (self $status) => $status->value, self::cases());
    }

    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }

        return $labels;
    }
}

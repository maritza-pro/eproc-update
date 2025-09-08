<?php

declare(strict_types = 1);

namespace App\Enums;

enum ProcurementStatus: string
{
    case ComingSoon = 'coming_soon';
    case Ongoing = 'ongoing';
    case Finished = 'finished';

    public function getLabel(): string
    {
        return match ($this) {
            self::ComingSoon => (string) __('Coming Soon'),
            self::Ongoing => (string) __('On Going'),
            self::Finished => (string) __('Finished'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ComingSoon => 'warning',
            self::Ongoing => 'success',
            self::Finished => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ComingSoon => 'heroicon-o-clock',
            self::Ongoing => 'heroicon-o-bolt',
            self::Finished => 'heroicon-o-check-circle',
        };
    }
}
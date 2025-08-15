<?php

declare(strict_types = 1);

namespace App\Enums;

enum VendorStatus: string
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';

    /**
     * Get the color associated with the vendor status.
     * Returns the color string for the current status.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    /**
     * Get the icon for the vendor status.
     * Returns the corresponding icon class name based on the status.
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Approved => 'heroicon-o-check-circle',
            self::Rejected => 'heroicon-o-x-circle',
        };
    }

    /**
     * Get the label for the vendor status.
     * Returns a human-readable label for the enum case.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}

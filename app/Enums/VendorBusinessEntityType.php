<?php

declare(strict_types = 1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VendorBusinessEntityType: string implements HasLabel
{
    case PT = 'pt';
    case CV = 'cv';
    case Perseorangan = 'perseorangan';
    case Yayasan = 'yayasan';

    /**
     * Get the label for the vendor status.
     * Returns a human-readable label for the enum case.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PT => 'PT (Perseroan Terbatas)',
            self::CV => 'CV (Commanditaire Vennootschap)',
            self::Perseorangan => 'Perseorangan',
            self::Yayasan => 'Yayasan',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::PT         => 'PT. ',
            self::CV         => 'CV. ',
            self::Yayasan    => 'Yayasan ',
            self::Perseorangan => 'Perseorangan',
        };
    }

    public static function fromMixed(null|string|self $value): ?self
    {
        return $value instanceof self ? $value
             : (is_string($value) ? self::tryFrom($value) : null);
    }
}

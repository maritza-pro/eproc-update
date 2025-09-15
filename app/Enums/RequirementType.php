<?php

declare(strict_types = 1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RequirementType: string implements HasLabel
{
    case Checkbox = 'checkbox';
    case Date = 'date';
    case File = 'file';
    case Number = 'number';
    case Radio = 'radio';
    case Select = 'select';
    case Text = 'text';
    case Textarea = 'textarea';

    public function getLabel(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Textarea => 'Textarea',
            self::Number => 'Number',
            self::Radio => 'Radio Button',
            self::Checkbox => 'Checkbox',
            self::Select => 'Select',
            self::Date => 'Date',
            self::File => 'File Upload',
        };
    }

    public function isChoice(): bool
    {
        return in_array($this, [self::Checkbox, self::Radio, self::Select], true);
    }
}

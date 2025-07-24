<?php

namespace App\Concerns\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Gate
{
    public function defineGates(): array
    {
        return [
            "{$this->getModelLabel()}.viewAny" => "Allows viewing the {$this->getModelLabel()} list",
            "{$this->getModelLabel()}.view" => "Allows viewing {$this->getModelLabel()} detail",
            "{$this->getModelLabel()}.create" => "Allows creating a new {$this->getModelLabel()}",
            "{$this->getModelLabel()}.edit" => "Allows updating {$this->getModelLabel()}",
            "{$this->getModelLabel()}.delete" => "Allows deleting {$this->getModelLabel()}",
            "{$this->getModelLabel()}.withoutGlobalScope" => "Allows viewing {$this->getModelLabel()} without global scope",
        ];
    }

    public static function canCreate(): bool
    {
        if (Auth::user()?->can(static::getModelLabel() . '.withoutGlobalScope')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.create');
    }

    public static function canDelete(Model $record): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') && Auth::user()->can(static::getModelLabel() . '.delete')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.delete') && $record->user_id == Auth::id();
    }

    public static function canEdit(Model $record): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') && Auth::user()->can(static::getModelLabel() . '.edit')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.edit') && $record->user_id == Auth::id();
    }

    public static function canView(Model $record): bool
    {
        if (Auth::user()->can(static::getModelLabel() . '.withoutGlobalScope') && Auth::user()->can(static::getModelLabel() . '.view')) {
            return true;
        }

        return Auth::user()->can(static::getModelLabel() . '.view') && $record->user_id == Auth::id();
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->can(static::getModelLabel() . '.viewAny');
    }
}

<?php

declare(strict_types = 1);

namespace App\Concerns\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Gate
{
    /**
     * Define the gates for this resource.
     *
     * Returns an array of gate definitions with their descriptions.
     */
    public function defineGates(): array
    {
        return [
            "{$this->getModelLabel()}.viewAny" => __('Allows viewing the :label list', ['label' => $this->getModelLabel()]),
            "{$this->getModelLabel()}.view" => __('Allows viewing :label detail', ['label' => $this->getModelLabel()]),
            "{$this->getModelLabel()}.create" => __('Allows creating a new :label', ['label' => $this->getModelLabel()]),
            "{$this->getModelLabel()}.edit" => __('Allows updating :label', ['label' => $this->getModelLabel()]),
            "{$this->getModelLabel()}.delete" => __('Allows deleting :label', ['label' => $this->getModelLabel()]),
            "{$this->getModelLabel()}.withoutGlobalScope" => __('Allows viewing :label without global scope', ['label' => $this->getModelLabel()]),
            "{$this->getModelLabel()}.validate" => __('Allows validating :label', ['label' => $this->getModelLabel()]),
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

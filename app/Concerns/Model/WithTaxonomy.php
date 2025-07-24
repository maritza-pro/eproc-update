<?php

namespace App\Concerns\Model;

use Illuminate\Database\Eloquent\Model;

trait WithTaxonomy
{
    public static function bootWithTaxonomy(): void
    {
        self::creating(function (Model $model): void {
            $modelClass = static::class;
            $model->setAttribute('model', $modelClass);
        });
    }
}

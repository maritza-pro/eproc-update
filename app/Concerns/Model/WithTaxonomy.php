<?php

declare(strict_types = 1);

namespace App\Concerns\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait WithTaxonomy
{
    public static function bootWithTaxonomy(): void
    {
        self::creating(function (Model $model): void {
            $modelClass = static::class;
            $model->setAttribute('model', $modelClass);
        });

        self::saving(function (Model $model): void {
            if (empty($model->code)) {
                $model->setAttribute('code', strtoupper(Str::random(8)));
            }

            if (empty($model->type)) {
                $model->setAttribute(
                    'type',
                    Str::of(class_basename($model))->headline()->slug()->toString()
                );
            }

            $model->setAttribute(
                'slug',
                Str::of($model->code . ' ' . class_basename($model->name))->headline()->slug()->toString()
            );
        });
    }
}

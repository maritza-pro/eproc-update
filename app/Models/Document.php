<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Document extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'filename',
        'path',
        'type',
    ];

    /**
     * Get the parent documentable model.
     *
     * Defines a polymorphic relationship to the documentable model.
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the options for logging activity.
     *
     * Defines the default options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

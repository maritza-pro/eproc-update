<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaxonomyRelation extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'taxonomy_id',
        'relationable_id',
        'relationable_type',
    ];

    /**
     * Get the options for logging activity.
     *
     * Defines the default options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the parent relationable model.
     * Defines a polymorphic relation to the relationable model.
     */
    public function relationable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the taxonomy associated with the relation.
     * Defines a belongs-to relationship with the Taxonomy model.
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }
}

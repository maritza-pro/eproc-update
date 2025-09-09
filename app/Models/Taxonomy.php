<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Taxonomy extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'model',
        'name',
        'slug',
        'type',
        'code',
        'description',
        'parent_id',
        'text_color',
        'background_color',
        'is_active',
        'is_system',
    ];

    protected $table = 'taxonomies';

    /**
     * Get the children taxonomies.
     *
     * Defines a has-many relationship with child taxonomy records.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'parent_id');
    }

    /**
     * Get activity log options.
     *
     * Defines the options for logging activity for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the parent taxonomy.
     * Defines an inverse one-to-many relationship with itself.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id');
    }

    // public function relationables(string $modelClass): MorphToMany
    // {
    //     return $this->morphedByMany($modelClass, 'relationable', 'taxonomy_relations');
    // }

    /**
     * Get the taxonomy relations.
     * Returns the HasMany relations to TaxonomyRelation.
     */
    public function relations(): HasMany
    {
        return $this->hasMany(TaxonomyRelation::class);
    }

    /**
     * Get the vendors associated with the taxonomy.
     *
     * Defines a has-many relationship with the Vendor model.
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
}

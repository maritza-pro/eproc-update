<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Taxonomy extends Model
{
    use LogsActivity,
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

    public function children(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'parent_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id');
    }

    public function relationables(string $modelClass): MorphToMany
    {
        return $this->morphedByMany($modelClass, 'relationable', 'taxonomy_relations');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(TaxonomyRelation::class);
    }
}

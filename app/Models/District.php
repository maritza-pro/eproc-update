<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class District extends Model
{
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'city_id',
        'latitude',
        'longitude',
    ];

    /**
     * Get the city associated with the district.
     *
     * Defines a belongs-to relationship with the City model.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
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
     * Get the villages associated with the district.
     *
     * Defines a has-many relationship with the Village model.
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'district_id', 'id');
    }
}

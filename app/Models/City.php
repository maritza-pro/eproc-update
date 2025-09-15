<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class City extends Model
{
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'province_id',
        'latitude',
        'longitude',
    ];

    /**
     * Get the districts associated with the city.
     * Defines a HasMany relationship with the District model.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'city_id', 'id');
    }

    /**
     * Get the options for logging activity.
     *
     * Configures the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the province associated with the city.
     * Defines a belongs-to relationship with the Province model.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    /**
     * Get the villages associated with the city.
     *
     * Defines a HasManyThrough relationship to retrieve villages via districts.
     */
    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(
            Village::class,
            District::class,
            'city_id',
            'district_id',
            'id',
            'id'
        );
    }
}

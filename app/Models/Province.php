<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Province extends Model
{
    //
    use HasRelationships,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'country_id',
        'latitude',
        'longitude',
    ];

    /**
     * Get the cities associated with the province.
     *
     * Defines a one-to-many relationship with the City model.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'province_id', 'id');
    }

    /**
     * Get the country.
     *
     * Defines a belongs-to relationship with the Country model.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * Get the districts associated with the province.
     *
     * Defines a HasManyThrough relationship with District model.
     */
    public function districts(): HasManyThrough
    {
        return $this->hasManyThrough(
            District::class,
            City::class,
            'province_id',
            'city_id',
            'id',
            'id'
        );
    }

    /**
     * Get activitylog options.
     *
     * Defines the options for logging activity for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the villages associated with the province.
     *
     * Defines a deep relationship to retrieve villages through cities and districts.
     */
    public function villages(): HasManyDeep
    {
        return $this->hasManyDeep(
            Village::class,
            [City::class, District::class],
            [
                'province_id',
                'city_id',
                'district_id',
            ]
        );
    }
}

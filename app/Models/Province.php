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
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city(): HasMany
    {
        return $this->hasMany(City::class, 'province_id', 'id');
    }

    public function district(): HasManyThrough
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

    public function village(): HasManyDeep
    {
        return $this->hasManyDeep(
            \App\Models\Village::class,
            [\App\Models\City::class, \App\Models\District::class],
            [
                'province_id',
                'city_id',
                'district_id',
            ]
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

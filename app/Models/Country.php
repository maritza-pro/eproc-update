<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Country extends Model
{
    //
    use LogsActivity,
        SoftDeletes,
        HasRelationships;

    protected $fillable = [
        'name',
        'code',
        'currency',
    ];

    public function province(): HasMany
    {
        return $this->hasMany(Province::class, 'country_id', 'id');
    }

    public function city(): HasManyThrough
    {
        return $this->hasManyThrough(
            City::class,
            Province::class,
            'country_id',
            'province_id',
            'id',
            'id'
        );
    }

    public function district()
    {
        return $this->hasManyDeep(
            \App\Models\District::class,
            [\App\Models\Province::class, \App\Models\City::class],
            [
                'country_id',
                'province_id',
                'city_id'
            ]
        );
    }

    public function village()
    {
        return $this->hasManyDeep(
            \App\Models\Village::class,
            [\App\Models\Province::class, \App\Models\City::class, \App\Models\District::class],
            [
                'country_id',
                'province_id',
                'city_id',
                'district_id'
            ]
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Country extends Model
{
    //
    use HasRelationships,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'currency',
        'iso',
        'num_code',
        'msisdn_code',
        'latitude',
        'longitude',
    ];


    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class, 'country_id', 'id');
    }

    public function cities(): HasManyThrough
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

    public function districts(): HasManyDeep
    {
        return $this->hasManyDeep(
            District::class,
            [Province::class, City::class],
            [
                'country_id',
                'province_id',
                'city_id',
            ]
        );
    }

    public function villages(): HasManyDeep
    {
        return $this->hasManyDeep(
            Village::class,
            [Province::class, City::class, District::class],
            [
                'country_id',
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

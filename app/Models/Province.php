<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
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

    public function city()
    {
        return $this->hasMany(City::class, 'province_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function district()
    {
        return $this->hasManyThrough(
            District::class,
            City::class,
            'province_id',
            'city_id',
            'id',
            'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function village()
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
}

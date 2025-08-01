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

class City extends Model
{
    //
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'name',
        'province_id',
        'latitude',
        'longitude',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'city_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

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

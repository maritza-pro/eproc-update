<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Country extends Model
{
    //
    use LogsActivity,
        SoftDeletes;

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
            'id');
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

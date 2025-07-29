<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BusinessField extends Model
{
    use Cachable,
        HasFactory,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'code',
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
}

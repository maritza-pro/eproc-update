<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bank extends Model
{
    //
    use LogsActivity,
        Cachable,
        SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

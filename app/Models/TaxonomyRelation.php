<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TaxonomyRelation extends Model
{
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'taxonomy_id',
        'relationable_id',
        'relationable_type',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

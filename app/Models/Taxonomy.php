<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Taxonomy extends Model
{
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'model',
        'name',
        'slug',
        'type',
        'code',
        'description',
        'parent_id',
        'text_color',
        'background_color',
        'is_active',
        'is_system',
    ];

    protected $table = 'taxonomies';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

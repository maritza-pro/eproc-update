<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Survey extends Model
{
    use LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SurveyCategory::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }
}

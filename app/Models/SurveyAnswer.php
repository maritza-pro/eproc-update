<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SurveyAnswer extends Model
{
    //
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'survey_id',
        'points',
        'max_points',
        'answered',
        'comment',
        'is_reviewed',
    ];

    public function answerable(): MorphTo
    {
        return $this->morphTo();
    }

    public function details(): HasMany
    {
        return $this->hasMany(SurveyAnswerDetail::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}

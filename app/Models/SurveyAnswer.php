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

    /**
     * Get the parent answerable model.
     *
     * Defines a polymorphic relation to the answerable model.
     */
    public function answerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the details associated with the survey answer.
     *
     * Defines a has-many relationship with SurveyAnswerDetail.
     */
    public function details(): HasMany
    {
        return $this->hasMany(SurveyAnswerDetail::class);
    }

    /**
     * Get the options for logging activity.
     *
     * Defines the default options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the survey associated with the survey answer.
     *
     * Defines a belongs-to relationship with the Survey model.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}

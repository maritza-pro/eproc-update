<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SurveyAnswerDetail extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'answer_id',
        'question_id',
        'option_id',
        'answers',
        'points',
    ];

    /**
     * Get the survey answer associated with the detail.
     *
     * Defines a relationship to the SurveyAnswer model.
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(SurveyAnswer::class);
    }

    /**
     * Get the options for logging activity.
     *
     * Configures activity logging options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the survey question option.
     *
     * Defines a relationship to the SurveyQuestionOption model.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestionOption::class);
    }

    /**
     * Get the question associated with the survey answer detail.
     *
     * Defines a belongs-to relationship with the SurveyQuestion model.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}

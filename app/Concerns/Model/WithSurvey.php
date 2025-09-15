<?php

declare(strict_types=1);

namespace App\Concerns\Model;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyAnswerDetail;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait WithSurvey
{
    /**
     * Get the survey answer.
     *
     * Defines a one-to-one relationship with the SurveyAnswer model.
     */
    public function answer(): MorphOne
    {
        return $this->morphOne(SurveyAnswer::class, 'answerable');
    }

    /**
     * Get the survey answer details.
     *
     * Returns the survey answer details through the survey answer.
     */
    public function answerDetails(): HasManyThrough
    {
        return $this->hasManyThrough(SurveyAnswerDetail::class, SurveyAnswer::class);
    }

    /**
     * Get the survey questions.
     *
     * Returns related survey questions through the survey model.
     */
    public function surverQuestions(): HasManyThrough
    {
        return $this->hasManyThrough(SurveyQuestion::class, Survey::class);
    }

    /**
     * Get the survey.
     *
     * Defines a belongs-to relationship with the Survey model.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}

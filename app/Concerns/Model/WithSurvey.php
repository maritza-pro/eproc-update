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
    public function answer(): MorphOne
    {
        return $this->morphOne(SurveyAnswer::class, 'answerable');
    }

    public function answerDetails(): HasManyThrough
    {
        return $this->hasManyThrough(SurveyAnswerDetail::class, SurveyAnswer::class);
    }

    public function surverQuestions(): HasManyThrough
    {
        return $this->hasManyThrough(SurveyQuestion::class, Survey::class);
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}

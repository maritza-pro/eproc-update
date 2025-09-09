<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SurveyQuestion extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    public const array TYPES = [
        self::TYPE_TEXT,
        self::TYPE_RADIO,
        self::TYPE_CHECKBOX,
        self::TYPE_SELECT,
        self::TYPE_DATE,
        self::TYPE_FILE,
    ];

    public const string TYPE_CHECKBOX = 'checkbox';

    public const string TYPE_DATE = 'date';

    public const string TYPE_FILE = 'file';

    public const string TYPE_RADIO = 'radio';

    public const string TYPE_SELECT = 'select';

    public const string TYPE_TEXT = 'text';

    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'description',
        'required',
        'points',
    ];

    /**
     * Get the options for logging activity.
     *
     * Defines the log options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the options for the survey question.
     *
     * Defines a relationship to retrieve associated question options.
     */
    public function options(): HasMany
    {
        return $this->hasMany(SurveyQuestionOption::class);
    }

    /**
     * Get the survey associated with the question.
     * Defines a relationship to the Survey model.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}

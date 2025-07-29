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

    const array TYPES = [
        self::TYPE_TEXT,
        self::TYPE_RADIO,
        self::TYPE_CHECKBOX,
        self::TYPE_SELECT,
        self::TYPE_DATE,
        self::TYPE_FILE,
    ];

    const string TYPE_CHECKBOX = 'checkbox';

    const string TYPE_DATE = 'date';

    const string TYPE_FILE = 'file';

    const string TYPE_RADIO = 'radio';

    const string TYPE_SELECT = 'select';

    const string TYPE_TEXT = 'text';

    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'description',
        'required',
        'points',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function options(): HasMany
    {
        return $this->hasMany(SurveyQuestionOption::class);
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}

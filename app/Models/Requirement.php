<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RequirementType;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Requirement extends Model
{
    //
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $casts = [
        'type' => RequirementType::class,
        'is_required' => 'bool',
        'points' => 'int',
    ];

    protected $fillable = [
        'procurement_schedule_id',
        'title',
        'type',
        'is_required',
        'description',
        'points',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function procurementSchedule(): BelongsTo
    {
        return $this->belongsTo(ProcurementSchedule::class);
    }

    public function requirementOptions(): HasMany
    {
        return $this->hasMany(RequirementOption::class);
    }
}

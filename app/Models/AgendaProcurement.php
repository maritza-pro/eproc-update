<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\ProcurementStatus;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AgendaProcurement extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'agenda_id',
        'procurement_id',
        'is_submission_needed',
        'start_date',
        'end_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): ProcurementStatus {
                $now = Carbon::now();
                $startDate = Carbon::parse($attributes['start_date']);
                $endDate = isset($attributes['end_date']) ? Carbon::parse($attributes['end_date']) : null;

                if ($startDate->isFuture()) {
                    return ProcurementStatus::ComingSoon;
                }

                if ($endDate && $endDate->isPast()) {
                    return ProcurementStatus::Finished;
                }

                return ProcurementStatus::Ongoing;
            },
        );
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }
}

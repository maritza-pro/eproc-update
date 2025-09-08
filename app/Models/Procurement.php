<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\ProcurementStatus;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Procurement extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'description',
        'method',
        'start_date',
        'end_date',
        'value',
        'number',
        'quantity',
    ];

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

    /**
     * Get the bids for the procurement.
     *
     * Defines a has-many relationship with the Bid model.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function businessField(): BelongsTo
    {
        return $this->belongsTo(BusinessField::class);
    }

    /**
     * Get the contracts associated with the procurement.
     *
     * Defines a has-many relationship with the Contract model.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the options for logging activity.
     *
     * Configures the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the procurement items.
     *
     * Defines a has-many relationship with ProcurementItem model.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function procurementMethod(): BelongsTo
    {
        return $this->belongsTo(ProcurementMethod::class, 'method_id');
    }

    public function procurementType(): BelongsTo
    {
        return $this->belongsTo(ProcurementType::class, 'type_id');
    }
}

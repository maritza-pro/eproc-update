<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Evaluation extends Model
{
    //
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'bid_id',
        'technical_score',
        'price_score',
        'notes',
    ];

    /**
     * Get the bid associated with the evaluation.
     *
     * Defines a belongs-to relationship with the Bid model.
     */
    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
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
}

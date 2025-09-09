<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BidItem extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'bid_id',
        'procurement_item_id',
        'unit_price',
        'offered_quantity',
        'notes',
    ];

    /**
     * Get the bid associated with the bid item.
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

    /**
     * Get the procurement item associated with the bid item.
     * Defines a belongs-to relationship with ProcurementItem model.
     */
    public function procurementItem(): BelongsTo
    {
        return $this->belongsTo(ProcurementItem::class);
    }
}

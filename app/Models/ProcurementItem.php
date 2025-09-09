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

class ProcurementItem extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'procurement_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the bid items associated with the procurement item.
     *
     * Defines a has-many relationship with BidItem model.
     */
    public function bidItems(): HasMany
    {
        return $this->hasMany(BidItem::class);
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
     * Get the procurement associated with the item.
     *
     * Defines a belongs-to relationship with the Procurement model.
     */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    /**
     * Get the product associated with the procurement item.
     *
     * Defines a relationship to the Product model.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

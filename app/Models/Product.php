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

class Product extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    const array TYPES = [
        self::TYPE_GOODS,
        self::TYPE_SERVICES,
    ];

    const string TYPE_GOODS = 'goods';

    const string TYPE_SERVICES = 'services';

    protected $fillable = [
        'name',
        'type',
        'unit',
        'description',
        'self_estimated_price',
    ];

    /**
     * Get the bid items associated with the product.
     *
     * Defines a has-many relationship with BidItem model.
     */
    public function bidItems(): HasMany
    {
        return $this->hasMany(BidItem::class, 'procurement_item_id', 'id');
    }

    /**
     * Get the currency associated with the product.
     *
     * Defines a belongs-to relationship with the Currency model.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
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
     * Get the procurement items associated with the product.
     *
     * Defines a has-many relationship with ProcurementItem.
     */
    public function procurementItems(): HasMany
    {
        return $this->hasMany(ProcurementItem::class);
    }
}

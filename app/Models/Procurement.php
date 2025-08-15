<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Procurement extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    const array METHODS = [
        self::METHOD_TENDER,
        self::METHOD_DIRECT,
    ];

    const string METHOD_DIRECT = 'direct';

    const string METHOD_TENDER = 'tender';

    protected $fillable = [
        'title',
        'description',
        'method',
        'start_date',
        'end_date',
    ];

    /**
     * Get the bids for the procurement.
     *
     * Defines a has-many relationship with the Bid model.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
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
}

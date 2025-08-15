<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bid extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes;

    const array STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_EVALUATED,
        self::STATUS_REJECTED,
        self::STATUS_ACCEPTED,
    ];

    const string STATUS_ACCEPTED = 'accepted';

    const string STATUS_DRAFT = 'draft';

    const string STATUS_EVALUATED = 'evaluated';

    const string STATUS_REJECTED = 'rejected';

    const string STATUS_SUBMITTED = 'submitted';

    protected $fillable = [
        'vendor_id',
        'procurement_id',
        'notes',
        'status',
    ];

    /**
     * Get the evaluation associated with the bid.
     *
     * Defines a one-to-one relationship with the Evaluation model.
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    /**
     * Get the evaluations associated with the bid.
     *
     * Defines a hasMany relationship with the Evaluation model.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
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
     * Get the bid items.
     *
     * Defines a has-many relationship with BidItem.
     */
    public function items(): HasMany
    {
        return $this->hasMany(BidItem::class);
    }

    /**
     * Get the procurement associated with the bid.
     * Defines a belongs-to relationship with the Procurement model.
     */
    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    /**
     * Get the vendor associated with the bid.
     * Defines a belongs-to relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

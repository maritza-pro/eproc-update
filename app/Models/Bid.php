<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bid extends Model
{
    use LogsActivity,
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

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function items(): HasMany
    {
        return $this->hasMany(BidItem::class);
    }

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

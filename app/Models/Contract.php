<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contract extends Model
{
    use LogsActivity,
        SoftDeletes;

    const array STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ACTIVE,
        self::STATUS_COMPLETED,
        self::STATUS_TERMINATED,
    ];

    const string STATUS_ACTIVE = 'active';

    const string STATUS_COMPLETED = 'completed';

    const string STATUS_DRAFT = 'draft';

    const string STATUS_TERMINATED = 'terminated';

    protected $fillable = [
        'procurement_id',
        'vendor_id',
        'contract_number',
        'signed_date',
        'value',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
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

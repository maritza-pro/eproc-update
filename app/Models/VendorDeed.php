<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VendorDeed extends Model implements HasMedia
{
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'deed_number',
        'deed_date',
        'deed_notary_name',
        'approval_number',
        'latest_amendment_number',
        'latest_amendment_date',
        'latest_amendment_notary',
        'latest_approval_number',
    ];

    /**
     * Get activity log options.
     *
     * Defines the options for logging activity on this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Register media collections.
     *
     * Defines the media collection for vendor deed attachments.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_deed_attachment')
            ->singleFile();
    }

    /**
     * Get the vendor associated with the deed.
     * Defines a one-to-one relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

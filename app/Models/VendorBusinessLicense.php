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

class VendorBusinessLicense extends Model implements HasMedia
{
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'license_number',
        'issued_at',
        'issued_by',
        'expires_at',
    ];

    /**
     * Get activity log options.
     *
     * Defines the configuration for logging activity.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Register media collections.
     *
     * Defines the media collection for vendor license attachments.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_license_attachment')
            ->singleFile();
    }

    /**
     * Get the vendor associated with the license.
     * Defines a one-to-one relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

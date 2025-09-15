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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VendorContact extends Model implements HasMedia
{
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'name',
        'position',
        'phone_number',
        'email',
        'identity_number',
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
     * Defines the media collections available for this model.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_contact_attachment')
            ->singleFile();
    }

    /**
     * Register media conversions.
     *
     * Defines a thumbnail conversion for media associated with this model.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->nonQueued()
            ->width(200)
            ->height(200);
    }

    /**
     * Get the vendor associated with the contact.
     * Defines a belongs-to relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

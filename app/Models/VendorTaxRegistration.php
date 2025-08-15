<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VendorTaxRegistration extends Model implements HasMedia
{
    //
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'name',
        'address',
        'certificate_number',
        'confirmation_status',
        'tax_obligation',
        'registered_tax_office',
    ];

    /**
     * Get activity log options.
     *
     * Defines the options for logging activity for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Register media collections.
     *
     * Defines the media collection for vendor tax registration attachments.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_tax_registration_attachment')
            ->singleFile();
    }

    /**
     * Get the vendor associated with the tax registration.
     * Defines a one-to-one relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

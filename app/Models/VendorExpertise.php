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

class VendorExpertise extends Model implements HasMedia
{
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'expertise',
        'expertise_level',
        'description',
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
     * Defines the media collections available for this model.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_expertise_attachment')
            ->singleFile();
    }

    /**
     * Get the vendor associated with the expertise.
     * Defines a belongs-to relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

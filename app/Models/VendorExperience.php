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

class VendorExperience extends Model implements HasMedia
{
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'business_field_id',
        'project_name',
        'description',
        'location',
        'stakeholder',
        'contract_number',
        'start_date',
        'end_date',
        'project_value',
    ];

    /**
     * Get the business field associated with the vendor experience.
     * Defines a belongs-to relationship with the VendorBusiness model.
     */
    public function businessField(): BelongsTo
    {
        return $this->belongsTo(BusinessField::class);
    }

    /**
     * Get activitylog options.
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
     * Defines the media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_experience_attachment')
            ->singleFile();
    }

    /**
     * Get the vendor associated with the experience.
     * Defines a belongs-to relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

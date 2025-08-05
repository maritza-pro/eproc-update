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

class VendorDeed extends Model implements HasMedia
{
    //
    use LogsActivity,
        Cachable,
        SoftDeletes,
        InteractsWithMedia;

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

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('deed_attachment') 
            ->singleFile(); 
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(200)
              ->height(200)
              ->nonQueued(); 
    }
}

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

class VendorBusinessCertificate extends Model implements HasMedia
{
    //
    use LogsActivity,
        Cachable,
        InteractsWithMedia,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'certificate_number',
        'issued_at',
        'issued_by',
        'expires_at',
        'classification',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_certificate_attachment')
            ->singleFile();
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

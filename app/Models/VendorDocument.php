<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VendorDocumentType;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VendorDocument extends Model implements HasMedia
{
    //
    use Cachable,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'type',
        'category',
        'document_number',
        'issue_date',
        'expiry_date',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'type' => VendorDocumentType::class,
            'properties' => 'array',
        ];
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_document_attachment')
            ->singleFile();
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BankVendor extends Model implements HasMedia
{
    use Cachable,
        HasFactory,
        InteractsWithMedia,
        LogsActivity,
        SoftDeletes;

    protected $fillable = [
        'bank_id',
        'vendor_id',
        'account_name',
        'account_number',
        'branch_name',
        'is_active',
        'is_default',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->is_default && $model->vendor_id) {
                static::query()->where('vendor_id', $model->vendor_id)
                    ->when($model->exists, fn ($bankVendor) => $bankVendor->whereKeyNot($model->getKey()))
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the bank associated with the bank vendor.
     * Defines a belongs-to relationship with the Bank model.
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

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
            ->addMediaCollection('recent_financial_report_attachment')
            ->singleFile();
    }

    /**
     * Get the vendor associated with the bank vendor.
     * Defines a belongs-to relationship with the Vendor model.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}

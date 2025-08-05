<?php

declare(strict_types = 1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VendorProfile extends Model
{
    //
    use LogsActivity,
        Cachable,
        SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'business_entity_type',
        'npwp',
        'nib',
        'head_office_address',
        'website',
        'established_year',
        'employee_count',
    ];


    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

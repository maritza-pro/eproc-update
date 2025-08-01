<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithSurvey;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Vendor extends Model
{
    use Cachable,
        LogsActivity,
        SoftDeletes,
        WithSurvey;

    protected $fillable = [
        'company_name',
        'email',
        'phone',
        'tax_number',
        'business_number',
        'license_number',
        'is_verified',
        'user_id',
        'business_field_id',
        'vendor_type_id',
        'bank_vendor_id',
    ];

    public function bankVendor(): BelongsTo
    {
        return $this->belongsTo(BankVendor::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function businessField(): BelongsTo
    {
        return $this->belongsTo(VendorBusiness::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function taxonomies(): MorphToMany
    {
        return $this->morphToMany(Taxonomy::class, 'relationable', 'taxonomy_relations')
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

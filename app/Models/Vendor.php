<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


class Vendor extends Model
{
    use LogsActivity,
        SoftDeletes;

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
    ];

	public function businessField(): BelongsTo
    {
        return $this->belongsTo(BusinessField::class);
    }

	 public function taxonomies(): MorphToMany
    {
        return $this->morphToMany(Taxonomy::class, 'relationable', 'taxonomy_relations')
					->withTimestamps();
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

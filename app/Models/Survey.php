<?php

declare(strict_types=1);

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Survey extends Model
{
    use Cachable,
        HasJsonRelationships,
        LogsActivity,
        SoftDeletes;

    public const array TYPES = [
        self::TYPE_VENDOR,
        self::TYPE_PROCUREMENT,
    ];

    public const string TYPE_PROCUREMENT = 'procurement';

    public const string TYPE_VENDOR = 'vendor';

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'type',
        'properties',
    ];

    /**
     * Define cast attributes.
     *
     * Specifies the data types for certain attributes.
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    /**
     * Get the survey category.
     *
     * Defines a relationship to the survey's category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SurveyCategory::class);
    }

    /**
     * Get the options for logging activity.
     *
     * Configures the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * Get the survey's questions.
     *
     * Defines a HasMany relationship with SurveyQuestion.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    /**
     * Get the vendor business associated with the survey.
     *
     * Defines a relationship to the VendorBusiness model.
     */
    public function vendorBusiness(): Belongsto
    {
        return $this->belongsTo(BusinessField::class, 'properties->vendor_business');
    }

    /**
     * Get the vendor type relationship.
     *
     * Defines the relationship to the VendorType model.
     */
    public function vendorType(): Belongsto
    {
        return $this->belongsTo(VendorType::class, 'properties->vendor_type');
    }
}

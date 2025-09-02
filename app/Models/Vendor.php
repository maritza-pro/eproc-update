<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithSurvey;
use App\Enums\VendorStatus;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vendor extends Model implements HasMedia
{
    use Cachable,
        InteractsWithMedia,
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
        'user_id',
        'business_field_id',
        'vendor_type_id',
        'verification_status',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'is_blacklisted',
        'blacklist_reason',
    ];

    /**
     * Define cast attributes.
     *
     * Specifies the data types for certain attributes.
     */
    protected function casts(): array
    {
        return [
            'verification_status' => VendorStatus::class,
            'verified_at' => 'datetime',
            'is_blacklisted' => 'boolean',
        ];
    }

    /**
     * Get the bank vendors associated with the vendor.
     *
     * Defines a HasMany relationship with BankVendor model.
     */
    public function bankVendors(): HasMany
    {
        return $this->hasMany(BankVendor::class);
    }

    /**
     * Get the bids for the vendor.
     *
     * Defines a HasMany relationship with the Bid model.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the business field.
     * Defines the relationship to the VendorBusiness model.
     */
    public function businessField(): BelongsTo
    {
        return $this->belongsTo(VendorBusiness::class);
    }

    /**
     * Get the contracts associated with the vendor.
     *
     * Defines a HasMany relationship with the Contract model.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Configure activity logging options.
     *
     * Defines the default options for logging model activities.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('vendor_logo_attachment')
            ->singleFile();

        $this
            ->addMediaCollection('vendor_financial_report_attachment')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->nonQueued()
            ->width(200)
            ->height(200);
    }

    /**
     * Get the taxonomies associated with the vendor.
     * Defines a morph-to-many relationship with Taxonomy.
     */
    public function taxonomies(): MorphToMany
    {
        return $this->morphToMany(Taxonomy::class, 'relationable', 'taxonomy_relations')
            ->withTimestamps();
    }

    /**
     * Get the user associated with the vendor.
     * Defines a belongs-to relationship with the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor's business certificate.
     *
     * Defines a one-to-one relationship with VendorBusinessCertificate.
     */
    public function vendorBusinessCertificate(): HasOne
    {
        return $this->hasOne(VendorBusinessCertificate::class);
    }

    /**
     * Get the vendor's business license.
     *
     * Defines a one-to-one relationship with VendorBusinessLicense.
     */
    public function vendorBusinessLicense(): HasOne
    {
        return $this->hasOne(VendorBusinessLicense::class);
    }

    /**
     * Get the vendor's contacts.
     *
     * Defines a HasMany relationship with VendorContact.
     */
    public function vendorContacts(): HasMany
    {
        return $this->hasMany(VendorContact::class);
    }

    /**
     * Get the vendor's deed.
     *
     * Defines a one-to-one relationship with VendorDeed.
     */
    public function vendorDeed(): HasOne
    {
        return $this->hasOne(VendorDeed::class);
    }

    public function vendorDocuments(): HasMany
    {
        return $this->hasMany(VendorDocument::class);
    }

    /**
     * Get the vendor experiences.
     * Defines a HasMany relationship with VendorExperience.
     */
    public function vendorExperiences(): HasMany
    {
        return $this->hasMany(VendorExperience::class);
    }

    /**
     * Get the vendor's expertises.
     *
     * Defines a HasMany relationship with VendorExpertise.
     */
    public function vendorExpertises(): HasMany
    {
        return $this->hasMany(VendorExpertise::class);
    }

    /**
     * Get the vendor's profile.
     *
     * Defines a one-to-one relationship with the VendorProfile model.
     */
    public function vendorProfile(): HasOne
    {
        return $this->hasOne(VendorProfile::class);
    }

    /**
     * Get the vendor's tax registration.
     *
     * Defines a one-to-one relationship with VendorTaxRegistration.
     */
    public function vendorTaxRegistration(): HasOne
    {
        return $this->hasOne(VendorTaxRegistration::class);
    }

    public function vendorType(): BelongsTo
    {
        return $this->belongsTo(VendorType::class);
    }
}

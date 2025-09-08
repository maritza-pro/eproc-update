<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithTaxonomy;
use App\Models\Scopes\TaxonomyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([TaxonomyScope::class])]
class BusinessField extends Taxonomy
{
    use WithTaxonomy;

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    } 

    /**
     * Get the vendor experiences.
     *
     * Defines a has-many relationship with VendorExperience.
     */
    public function vendorExperiences(): HasMany
    {
        return $this->hasMany(VendorExperience::class);
    }

    /**
     * Get the vendors associated with the business.
     *
     * Defines a has-many relationship with the Vendor model.
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
}

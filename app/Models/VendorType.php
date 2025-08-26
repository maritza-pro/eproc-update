<?php

declare(strict_types = 1);

namespace App\Models;

use App\Concerns\Model\WithTaxonomy;
use App\Models\Scopes\TaxonomyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([TaxonomyScope::class])]
class VendorType extends Taxonomy
{
    use WithTaxonomy;

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
}
